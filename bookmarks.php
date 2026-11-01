
<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = "";

$upload_dir = "uploads/case-documents/";

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Upload document with metadata
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_document'])) {

    $case_id = (int) $_POST['case_id'];

    $title = trim($_POST['title']);
    $document_type = trim($_POST['document_type']);
    $court = trim($_POST['court']);
    $judge = trim($_POST['judge']);
    $year = $_POST['year'] !== '' ? (int) $_POST['year'] : null;
    $act_name = trim($_POST['act_name']);
    $section = trim($_POST['section']);
    $content = trim($_POST['content']);

    $valid_types = [
        'Judgment',
        'Order',
        'Petition',
        'Legal Notice',
        'Evidence',
        'Agreement',
        'Other'
    ];

    // Verify case ownership
    $check = $conn->prepare("
        SELECT cid FROM cases
        WHERE cid = ? AND owner_id = ?
    ");

    $check->bind_param("ii", $case_id, $user_id);
    $check->execute();

    $case_valid = $check->get_result()->num_rows > 0;

    if (!$case_valid) {
        $error = "Please select a valid case.";
    } elseif ($title === '' || !in_array($document_type, $valid_types, true)) {
        $error = "Please enter a title and select a valid document type.";
    } elseif (!isset($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
        $error = "Please select a file to upload.";
    } else {

        $file = $_FILES['document'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        $allowed_extensions = [
            'pdf', 'doc', 'docx', 'txt',
            'jpg', 'jpeg', 'png'
        ];

        $allowed_mimes = [
            'pdf'  => ['application/pdf'],
            'doc'  => ['application/msword', 'application/CDFV2'],
            'docx' => [
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/zip'
            ],
            'txt'  => ['text/plain'],
            'jpg'  => ['image/jpeg'],
            'jpeg' => ['image/jpeg'],
            'png'  => ['image/png']
        ];

        if (!in_array($extension, $allowed_extensions, true)) {
            $error = "This file type is not supported.";
        } elseif ($file['size'] > 10 * 1024 * 1024) {
            $error = "File size must be less than 10 MB.";
        } else {

            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($file['tmp_name']);

            if (!in_array($mime, $allowed_mimes[$extension], true)) {
                $error = "Invalid file. Please select a valid document.";
            } else {

                $filename = bin2hex(random_bytes(16)) . "." . $extension;
                $destination = $upload_dir . $filename;

                if (move_uploaded_file($file['tmp_name'], $destination)) {

                    try {
                        $conn->begin_transaction();

                        // Insert file path into documents
                        $stmt = $conn->prepare("
                            INSERT INTO documents (path, case_id)
                            VALUES (?, ?)
                        ");

                        $stmt->bind_param("si", $destination, $case_id);
                        $stmt->execute();

                        $document_id = $conn->insert_id;

                        // Insert metadata into legal_documents
                        $stmt2 = $conn->prepare("
                            INSERT INTO legal_documents
                            (
                                d_id,
                                title,
                                document_type,
                                court,
                                judge,
                                year,
                                act_name,
                                section,
                                content
                            )
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                        ");

                        $stmt2->bind_param(
                            "issssisss",
                            $document_id,
                            $title,
                            $document_type,
                            $court,
                            $judge,
                            $year,
                            $act_name,
                            $section,
                            $content
                        );

                        $stmt2->execute();

                        $conn->commit();

                        header("Location: bookmarks.php?uploaded=1");
                        exit();

                    } catch (Exception $e) {

                        $conn->rollback();

                        if (is_file($destination)) {
                            unlink($destination);
                        }

                        $error = "Unable to save document. Please try again.";
                    }

                } else {
                    $error = "Unable to upload the file.";
                }
            }
        }
    }
}

// Delete document
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_document'])) {

    $document_id = (int) $_POST['document_id'];

    // Check document ownership
    $check = $conn->prepare("
        SELECT documents.path
        FROM documents
        INNER JOIN cases ON documents.case_id = cases.cid
        WHERE documents.d_id = ?
        AND cases.owner_id = ?
    ");

    $check->bind_param("ii", $document_id, $user_id);
    $check->execute();

    $document = $check->get_result()->fetch_assoc();

    if ($document) {

        $delete = $conn->prepare("
            DELETE documents
            FROM documents
            INNER JOIN cases ON documents.case_id = cases.cid
            WHERE documents.d_id = ?
            AND cases.owner_id = ?
        ");

        $delete->bind_param("ii", $document_id, $user_id);

        if ($delete->execute()) {

            // Remove physical file
            $filename = basename($document['path']);
            $file_path = $upload_dir . $filename;

            if (is_file($file_path)) {
                unlink($file_path);
            }

            header("Location: bookmarks.php?deleted=1");
            exit();
        }
    }

    $error = "Unable to delete document.";
}

// Fetch cases
$case_stmt = $conn->prepare("
    SELECT cid, applicant, defendent
    FROM cases
    WHERE owner_id = ?
    ORDER BY cid DESC
");

$case_stmt->bind_param("i", $user_id);
$case_stmt->execute();
$cases = $case_stmt->get_result();

// Search documents
$search = trim($_GET['search'] ?? '');

$sql = "
    SELECT
        documents.d_id,
        documents.path,
        documents.case_id,
        legal_documents.title,
        legal_documents.document_type,
        legal_documents.court,
        legal_documents.judge,
        legal_documents.year,
        legal_documents.act_name,
        legal_documents.section,
        cases.applicant,
        cases.defendent
    FROM documents
    INNER JOIN legal_documents
        ON documents.d_id = legal_documents.d_id
    INNER JOIN cases
        ON documents.case_id = cases.cid
    WHERE cases.owner_id = ?
";

$params = [$user_id];
$types = "i";

if ($search !== '') {

    $sql .= " AND (
        legal_documents.title LIKE ?
        OR legal_documents.document_type LIKE ?
        OR legal_documents.court LIKE ?
        OR legal_documents.act_name LIKE ?
        OR cases.applicant LIKE ?
        OR cases.defendent LIKE ?
    )";

    $term = "%" . $search . "%";

    for ($i = 0; $i < 6; $i++) {
        $params[] = $term;
    }

    $types .= "ssssss";
}

$sql .= " ORDER BY legal_documents.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();

$documents = $stmt->get_result();

// Total documents
$count_stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM documents
    INNER JOIN cases ON documents.case_id = cases.cid
    INNER JOIN legal_documents ON documents.d_id = legal_documents.d_id
    WHERE cases.owner_id = ?
");

$count_stmt->bind_param("i", $user_id);
$count_stmt->execute();

$total_documents = $count_stmt->get_result()->fetch_assoc()['total'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Documents - LegalEase</title>

    <link rel="stylesheet" href="css/bookmarks.css">
</head>

<body>

<div class="sidebar">

    <h2>LegalEase</h2>

    <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="clients.php">Clients</a>
        <a href="cases.php">Cases</a>
        <a href="research.php">Legal Research</a>
        <a href="bookmarks.php" class="active">Documents</a>
        <a href="hearings.php">Hearings</a>
        <a href="tasks.php">Tasks & Notes</a>
    </div>

    <div class="sidebar-bottom">
        <a href="profile.php">My Profile</a>
        <a href="logout.php">Logout</a>
    </div>

</div>

<div class="main-content">

    <div class="topbar">
        <h2>Document Storage</h2>

        <span>
            Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
        </span>
    </div>

    <div class="page-content">

        <div class="page-heading">

            <div>
                <h2>My Documents</h2>
                <p>Upload and manage documents related to your cases.</p>
            </div>

            <span class="count">
                <?php echo $total_documents; ?> Documents
            </span>

        </div>

        <?php if (isset($_GET['uploaded'])): ?>
            <div class="success-message">
                Document uploaded successfully.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['deleted'])): ?>
            <div class="success-message">
                Document deleted successfully.
            </div>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Upload Form -->

        <div class="upload-card">

            <h3>Upload New Document</h3>

            <form method="POST" enctype="multipart/form-data">

                <div class="form-grid">

                    <div class="form-group">
                        <label>Document Title</label>

                        <input
                            type="text"
                            name="title"
                            placeholder="Enter document title"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label>Document Type</label>

                        <select name="document_type" required>
                            <option value="">Select document type</option>
                            <option value="Judgment">Judgment</option>
                            <option value="Order">Court Order</option>
                            <option value="Petition">Petition</option>
                            <option value="Legal Notice">Legal Notice</option>
                            <option value="Evidence">Evidence</option>
                            <option value="Agreement">Agreement</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Related Case</label>

                        <select name="case_id" required>
                            <option value="">Select a case</option>

                            <?php while ($case = $cases->fetch_assoc()): ?>

                                <option value="<?php echo $case['cid']; ?>">
                                    #<?php echo $case['cid']; ?> -
                                    <?php echo htmlspecialchars($case['applicant']); ?>
                                    vs
                                    <?php echo htmlspecialchars($case['defendent']); ?>
                                </option>

                            <?php endwhile; ?>

                        </select>
                    </div>

                    <div class="form-group">
                        <label>Upload File</label>

                        <input
                            type="file"
                            name="document"
                            accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png"
                            required
                        >

                        <small>PDF, DOC, DOCX, TXT, JPG, PNG. Maximum 10 MB.</small>
                    </div>

                    <div class="form-group">
                        <label>Court Name</label>

                        <input
                            type="text"
                            name="court"
                            placeholder="Enter court name"
                        >
                    </div>

                    <div class="form-group">
                        <label>Judge Name</label>

                        <input
                            type="text"
                            name="judge"
                            placeholder="Enter judge name"
                        >
                    </div>

                    <div class="form-group">
                        <label>Year</label>

                        <input
                            type="number"
                            name="year"
                            min="1800"
                            max="2100"
                            placeholder="e.g. 2025"
                        >
                    </div>

                    <div class="form-group">
                        <label>Act Name</label>

                        <input
                            type="text"
                            name="act_name"
                            placeholder="Enter Act name"
                        >
                    </div>

                    <div class="form-group">
                        <label>Section</label>

                        <input
                            type="text"
                            name="section"
                            placeholder="Enter section number"
                        >
                    </div>

                </div>

                <div class="form-group">
                    <label>Description / Document Content</label>

                    <textarea
                        name="content"
                        rows="4"
                        placeholder="Enter a short description or relevant document content..."
                    ></textarea>
                </div>

                <button type="submit" name="upload_document" class="upload-btn">
                    Upload Document
                </button>

            </form>

        </div>

        <!-- Search -->

        <div class="search-card">

            <form method="GET" class="search-form">

                <input
                    type="text"
                    name="search"
                    placeholder="Search by title, document type, court or case..."
                    value="<?php echo htmlspecialchars($search); ?>"
                >

                <button type="submit">Search</button>

                <?php if ($search !== ''): ?>
                    <a href="bookmarks.php" class="reset-btn">Reset</a>
                <?php endif; ?>

            </form>

        </div>

        <!-- Document List -->

        <div class="document-card">

            <h3>Uploaded Documents</h3>

            <div class="table-container">

                <table>

                    <thead>
                        <tr>
                            <th>Document Title</th>
                            <th>Type</th>
                            <th>Related Case</th>
                            <th>Court</th>
                            <th>Year</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php if ($documents->num_rows > 0): ?>

                            <?php while ($document = $documents->fetch_assoc()): ?>

                                <tr>

                                    <td>
                                        <strong>
                                            <?php echo htmlspecialchars($document['title']); ?>
                                        </strong>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($document['document_type']); ?>
                                    </td>

                                    <td>
                                        <a href="case-details.php?id=<?php echo $document['case_id']; ?>">
                                            <?php echo htmlspecialchars($document['applicant']); ?>
                                            vs
                                            <?php echo htmlspecialchars($document['defendent']); ?>
                                        </a>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($document['court'] ?: '-'); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($document['year'] ?: '-'); ?>
                                    </td>

                                    <td>

                                        <div class="actions">

                                            <a
                                                href="<?php echo htmlspecialchars($document['path']); ?>"
                                                target="_blank"
                                                class="view-btn"
                                            >
                                                View
                                            </a>

                                            <a
                                                href="<?php echo htmlspecialchars($document['path']); ?>"
                                                download
                                                class="download-btn"
                                            >
                                                Download
                                            </a>

                                            <form
                                                method="POST"
                                                onsubmit="return confirm('Delete this document?');"
                                            >

                                                <input
                                                    type="hidden"
                                                    name="document_id"
                                                    value="<?php echo $document['d_id']; ?>"
                                                >

                                                <button
                                                    type="submit"
                                                    name="delete_document"
                                                    class="delete-btn"
                                                >
                                                    Delete
                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                            <?php endwhile; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="6" class="no-data">
                                    No documents found. Upload your first case document.
                                </td>
                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

</body>
</html>
