
<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$case_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($case_id <= 0) {
    die("Invalid case ID.");
}

// Fetch case details
$stmt = $conn->prepare("
    SELECT cases.*, client.name AS client_name, client.phone AS client_phone
    FROM cases
    INNER JOIN client ON cases.client_id = client.client_id
    WHERE cases.cid = ? AND (cases.owner_id = ? OR cases.owner_id is NULL)
");

$stmt->bind_param("ii", $case_id, $user_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Case not found.");
}

$case = $result->fetch_assoc();

// Fetch case notes
$stmt = $conn->prepare("
    SELECT * FROM notes
    WHERE case_id = ?
    ORDER BY id DESC
");

$stmt->bind_param("i", $case_id);
$stmt->execute();
$notes = $stmt->get_result();

// Fetch case documents
$stmt = $conn->prepare("
    SELECT * FROM documents
    WHERE case_id = ?
    ORDER BY d_id DESC
");

$stmt->bind_param("i", $case_id);
$stmt->execute();
$documents = $stmt->get_result();

// Fetch case hearings
$stmt = $conn->prepare("
    SELECT * FROM hearing
    WHERE c_id = ?
    ORDER BY data DESC
");

$stmt->bind_param("i", $case_id);
$stmt->execute();
$hearings = $stmt->get_result();

// Fetch case tasks
$stmt = $conn->prepare("
    SELECT * FROM case_tasks
    WHERE case_id = ?
    ORDER BY created_at DESC
");

$stmt->bind_param("i", $case_id);
$stmt->execute();
$tasks = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Case Details - LegalEase</title>
    <link rel="stylesheet" href="css/case-details.css">
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
            <a href="cases.php" class="active">Cases</a>
            <a href="research.php">Legal Research</a>
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
                <h2>Case Details</h2>
                <p>View complete case information</p>
            </div>

            <div class="user-info">
                <?php echo htmlspecialchars($_SESSION['user_name']); ?>
            </div>
        </header>

        <div class="content">

            <!-- Page Header -->
            <div class="page-header">

                <div>
                    <h3>
                        <?php echo htmlspecialchars($case['applicant']); ?>
                        vs
                        <?php echo htmlspecialchars($case['defendent']); ?>
                    </h3>

                    <p>Case ID: #<?php echo $case['cid']; ?></p>
                </div>

                <div class="header-actions">
                    <a href="cases.php" class="back-btn">Back</a>

                    <a href="case-form.php?id=<?php echo $case_id; ?>" class="edit-btn">
                        Edit Case
                    </a>
                </div>

            </div>

            <!-- Case Information -->
            <section class="section-card">

                <h3>Case Information</h3>

                <div class="info-grid">

                    <div class="info-item">
                        <label>Applicant</label>
                        <p><?php echo htmlspecialchars($case['applicant']); ?></p>
                    </div>

                    <div class="info-item">
                        <label>Defendant</label>
                        <p><?php echo htmlspecialchars($case['defendent']); ?></p>
                    </div>

                    <div class="info-item">
                        <label>Client Name</label>
                        <p><?php echo htmlspecialchars($case['client_name']); ?></p>
                    </div>

                    <div class="info-item">
                        <label>Client Phone</label>
                        <p><?php echo htmlspecialchars($case['client_phone']); ?></p>
                    </div>

                    <div class="info-item">
                        <label>Judge</label>
                        <p><?php echo htmlspecialchars($case['judge'] ?: 'Not assigned'); ?></p>
                    </div>

                    <div class="info-item">
                        <label>Status</label>
                        <p>
                            <span class="status">
                                <?php echo htmlspecialchars($case['status']); ?>
                            </span>
                        </p>
                    </div>

                    <div class="info-item">
                        <label>Created On</label>
                        <p><?php echo date("d M Y", strtotime($case['created_at'])); ?></p>
                    </div>

                </div>

                <div class="description">
                    <label>Case Description</label>
                    <p>
                        <?php echo nl2br(htmlspecialchars($case['body'] ?: 'No description available.')); ?>
                    </p>
                </div>

            </section>

            <!-- Case Notes -->
            <section class="section-card">

                <div class="section-header">
                    <h3>Case Notes</h3>
                    <span><?php echo $notes->num_rows; ?> notes</span>
                </div>

                <?php if ($notes->num_rows > 0) { ?>

                    <?php while ($note = $notes->fetch_assoc()) { ?>

                        <div class="list-item">
                            <p><?php echo nl2br(htmlspecialchars($note['data'])); ?></p>
                        </div>

                    <?php } ?>

                <?php } else { ?>

                    <p class="empty">No notes added to this case.</p>

                <?php } ?>

            </section>

            <!-- Case Documents -->
            <section class="section-card">

                <div class="section-header">
                    <h3>Case Documents</h3>
                    <span><?php echo $documents->num_rows; ?> documents</span>
                </div>

                <?php if ($documents->num_rows > 0) { ?>

                    <?php while ($document = $documents->fetch_assoc()) { ?>

                        <div class="list-item document-item">

                            <p><?php echo htmlspecialchars(basename($document['path'])); ?></p>

                            <a
                                href="<?php echo htmlspecialchars($document['path']); ?>"
                                target="_blank"
                            >
                                View Document
                            </a>

                        </div>

                    <?php } ?>

                <?php } else { ?>

                    <p class="empty">No documents uploaded.</p>

                <?php } ?>

            </section>

            <!-- Hearings -->
            <section class="section-card">

                <div class="section-header">
                    <h3>Hearing History</h3>
                    <span><?php echo $hearings->num_rows; ?> hearings</span>
                </div>

                <?php if ($hearings->num_rows > 0) { ?>

                    <?php while ($hearing = $hearings->fetch_assoc()) { ?>

                        <div class="list-item">

                            <div>
                                <h4>
                                    <?php echo date("d M Y, h:i A", strtotime($hearing['data'])); ?>
                                </h4>

                                <p>
                                    <?php echo htmlspecialchars($hearing['status'] ?: 'No status'); ?>
                                </p>
                            </div>

                            <span>
                                <?php echo htmlspecialchars($hearing['conclusion'] ?: 'No conclusion'); ?>
                            </span>

                        </div>

                    <?php } ?>

                <?php } else { ?>

                    <p class="empty">No hearing records available.</p>

                <?php } ?>

            </section>

            <!-- Tasks -->
            <section class="section-card">

                <div class="section-header">
                    <h3>Case Tasks</h3>
                    <span><?php echo $tasks->num_rows; ?> tasks</span>
                </div>

                <?php if ($tasks->num_rows > 0) { ?>

                    <?php while ($task = $tasks->fetch_assoc()) { ?>

                        <div class="list-item">

                            <div>
                                <h4><?php echo htmlspecialchars($task['title']); ?></h4>
                                <p><?php echo htmlspecialchars($task['description']); ?></p>
                            </div>

                            <span>
                                <?php echo htmlspecialchars($task['status']); ?>
                            </span>

                        </div>

                    <?php } ?>

                <?php } else { ?>

                    <p class="empty">No tasks assigned to this case.</p>

                <?php } ?>

            </section>

        </div>

    </main>

</div>

</body>
</html>
