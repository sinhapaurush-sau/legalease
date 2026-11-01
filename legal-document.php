
<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$document_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($document_id <= 0) {
    die("Invalid document ID.");
}

// Fetch legal document
$stmt = $conn->prepare("
    SELECT * FROM legal_documents
    WHERE legal_document_id = ?
");

$stmt->bind_param("i", $document_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Legal document not found.");
}

$document = $result->fetch_assoc();

// Check whether document is bookmarked
$stmt = $conn->prepare("
    SELECT bookmark_id FROM bookmarks
    WHERE user_id = ? AND legal_document_id = ?
");

$stmt->bind_param("ii", $user_id, $document_id);
$stmt->execute();

$is_bookmarked = $stmt->get_result()->num_rows > 0;

// Handle bookmark action
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['bookmark'])) {

        if (!$is_bookmarked) {

            $stmt = $conn->prepare("
                INSERT INTO bookmarks (user_id, legal_document_id)
                VALUES (?, ?)
            ");

            $stmt->bind_param("ii", $user_id, $document_id);
            $stmt->execute();
        }

    } elseif (isset($_POST['remove_bookmark'])) {

        $stmt = $conn->prepare("
            DELETE FROM bookmarks
            WHERE user_id = ? AND legal_document_id = ?
        ");

        $stmt->bind_param("ii", $user_id, $document_id);
        $stmt->execute();
    }

    header("Location: legal-document.php?id=" . $document_id);
    exit();
}

$is_bookmarked = false;

$stmt = $conn->prepare("
    SELECT bookmark_id FROM bookmarks
    WHERE user_id = ? AND legal_document_id = ?
");

$stmt->bind_param("ii", $user_id, $document_id);
$stmt->execute();

$is_bookmarked = $stmt->get_result()->num_rows > 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($document['title']); ?> - LegalEase</title>
    <link rel="stylesheet" href="css/legal-document.css">
</head>
<body>

<div class="layout">

    <!-- Sidebar -->
    <aside class="sidebar">

        <div class="brand">
            <h2>LegalEase</h2>
            <p>Legal Management System</p>
        </div>

        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="clients.php">Clients</a>
            <a href="cases.php">Cases</a>
            <a href="research.php" class="active">Legal Research</a>
            <a href="bookmarks.php">Documents</a>
            <a href="hearings.php">Hearings</a>
            <a href="tasks.php">Tasks & Notes</a>
        </nav>

        <div class="sidebar-bottom">
            <a href="profile.php">My Profile</a>
            <a href="logout.php">Logout</a>
        </div>

    </aside>

    <!-- Main Content -->
    <main class="main-content">

        <header class="topbar">
            <div>
                <h2>Legal Research</h2>
                <p>Legal document details</p>
            </div>

            <div class="user-info">
                <?php echo htmlspecialchars($_SESSION['user_name']); ?>
            </div>
        </header>

        <div class="content">

            <div class="page-header">
                <a href="research.php" class="back-btn">
                    ← Back to Research
                </a>
            </div>

            <div class="document-card">

                <div class="document-header">

                    <div>
                        <span class="document-type">
                            <?php echo htmlspecialchars($document['document_type']); ?>
                        </span>

                        <h2>
                            <?php echo htmlspecialchars($document['title']); ?>
                        </h2>
                    </div>

                    <form method="POST">

                        <?php if ($is_bookmarked) { ?>

                            <button
                                type="submit"
                                name="remove_bookmark"
                                class="bookmark-btn saved"
                            >
                                Remove Bookmark
                            </button>

                        <?php } else { ?>

                            <button
                                type="submit"
                                name="bookmark"
                                class="bookmark-btn"
                            >
                                + Bookmark
                            </button>

                        <?php } ?>

                    </form>

                </div>

                <div class="metadata">

                    <div>
                        <label>Court</label>
                        <p><?php echo htmlspecialchars($document['court'] ?: 'Not specified'); ?></p>
                    </div>

                    <div>
                        <label>Judge</label>
                        <p><?php echo htmlspecialchars($document['judge'] ?: 'Not specified'); ?></p>
                    </div>

                    <div>
                        <label>Year</label>
                        <p><?php echo htmlspecialchars($document['year'] ?: 'Not specified'); ?></p>
                    </div>

                    <div>
                        <label>Act</label>
                        <p><?php echo htmlspecialchars($document['act_name'] ?: 'Not specified'); ?></p>
                    </div>

                    <div>
                        <label>Section</label>
                        <p><?php echo htmlspecialchars($document['section'] ?: 'Not specified'); ?></p>
                    </div>

                    <div>
                        <label>Added On</label>
                        <p><?php echo date("d M Y", strtotime($document['created_at'])); ?></p>
                    </div>

                </div>

                <div class="document-content">

                    <h3>Document Content</h3>

                    <div class="content-text">
                        <?php
                        echo nl2br(htmlspecialchars(
                            $document['content'] ?: 'No content available.'
                        ));
                        ?>
                    </div>

                </div>

            </div>

        </div>

    </main>

</div>

</body>
</html>
