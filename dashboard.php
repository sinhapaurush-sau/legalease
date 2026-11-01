
<?php
session_start();

require_once 'includes/db.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Total cases
$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM cases
    WHERE owner_id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$total_cases = $stmt->get_result()->fetch_assoc()['total'];


// Pending cases
$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM cases
    WHERE owner_id = ?
    AND status = 'Pending'
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$pending_cases = $stmt->get_result()->fetch_assoc()['total'];


// Total clients
$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM client
");

$total_clients = $result->fetch_assoc()['total'];


// Upcoming hearings
$stmt = $conn->prepare("
    SELECT hearing.*, cases.applicant, cases.defendent
    FROM hearing
    INNER JOIN cases ON hearing.c_id = cases.cid
    WHERE cases.owner_id = ?
    AND hearing.data >= NOW()
    ORDER BY hearing.data ASC
    LIMIT 5
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$hearings = $stmt->get_result();


// Recent cases
$stmt = $conn->prepare("
    SELECT cid, applicant, defendent, status, created_at
    FROM cases
    WHERE owner_id = ?
    ORDER BY created_at DESC
    LIMIT 5
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$recent_cases = $stmt->get_result();


// Pending tasks
$stmt = $conn->prepare("
    SELECT case_tasks.*, cases.applicant, cases.defendent
    FROM case_tasks
    INNER JOIN cases ON case_tasks.case_id = cases.cid
    WHERE case_tasks.assigned_to = ?
    AND case_tasks.status != 'Completed'
    ORDER BY case_tasks.deadline ASC
    LIMIT 5
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$tasks = $stmt->get_result();


// Bookmarked legal documents
$stmt = $conn->prepare("
    SELECT
        bookmarks.bookmark_id,
        bookmarks.created_at AS bookmarked_at,
        bookmarks.note,
        legal_documents.legal_document_id,
        legal_documents.title,
        legal_documents.document_type,
        legal_documents.court,
        legal_documents.judge,
        legal_documents.year,
        legal_documents.act_name,
        legal_documents.section
    FROM bookmarks
    INNER JOIN legal_documents
        ON bookmarks.legal_document_id = legal_documents.legal_document_id
    WHERE bookmarks.user_id = ?
    ORDER BY bookmarks.created_at DESC
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$bookmarked_documents = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard - LegalEase</title>

    <link rel="stylesheet" href="css/dashboard.css">
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
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="clients.php">Clients</a>
            <a href="cases.php">Cases</a>
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
                <h2>Dashboard</h2>

                <p>
                    Welcome back,
                    <?php echo htmlspecialchars($user_name); ?>!
                </p>
            </div>

            <div class="user-info">
                <?php echo htmlspecialchars($user_name); ?>
            </div>

        </header>


        <div class="content">


            <!-- Statistics -->

            <div class="stats-grid">

                <div class="stat-card">
                    <p>Total Cases</p>
                    <h2><?php echo $total_cases; ?></h2>
                </div>

                <div class="stat-card">
                    <p>Pending Cases</p>
                    <h2><?php echo $pending_cases; ?></h2>
                </div>

                <div class="stat-card">
                    <p>Total Clients</p>
                    <h2><?php echo $total_clients; ?></h2>
                </div>

                <div class="stat-card">
                    <p>Upcoming Hearings</p>
                    <h2><?php echo $hearings->num_rows; ?></h2>
                </div>

            </div>


            <!-- Recent Cases -->

            <section class="section-card">

                <div class="section-header">
                    <h3>Recent Cases</h3>
                    <a href="cases.php">View All</a>
                </div>

                <table>

                    <thead>
                        <tr>
                            <th>Case ID</th>
                            <th>Applicant</th>
                            <th>Defendant</th>
                            <th>Status</th>
                            <th>Created On</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php if ($recent_cases->num_rows > 0) { ?>

                            <?php while ($case = $recent_cases->fetch_assoc()) { ?>

                                <tr>

                                    <td>
                                        #<?php echo $case['cid']; ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($case['applicant']); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($case['defendent']); ?>
                                    </td>

                                    <td>
                                        <span class="status">
                                            <?php echo htmlspecialchars($case['status']); ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?php
                                        echo date(
                                            "d M Y",
                                            strtotime($case['created_at'])
                                        );
                                        ?>
                                    </td>

                                </tr>

                            <?php } ?>

                        <?php } else { ?>

                            <tr>
                                <td colspan="5" class="empty">
                                    No cases found.
                                </td>
                            </tr>

                        <?php } ?>

                    </tbody>

                </table>

            </section>


            <!-- Hearings and Tasks -->

            <div class="bottom-grid">


                <!-- Upcoming Hearings -->

                <section class="section-card">

                    <div class="section-header">
                        <h3>Upcoming Hearings</h3>
                        <a href="hearings.php">View All</a>
                    </div>

                    <?php if ($hearings->num_rows > 0) { ?>

                        <?php while ($hearing = $hearings->fetch_assoc()) { ?>

                            <div class="list-item">

                                <div>
                                    <h4>
                                        <?php echo htmlspecialchars($hearing['applicant']); ?>
                                        vs
                                        <?php echo htmlspecialchars($hearing['defendent']); ?>
                                    </h4>

                                    <p>
                                        <?php echo htmlspecialchars($hearing['status'] ?? 'Scheduled'); ?>
                                    </p>
                                </div>

                                <span>
                                    <?php
                                    echo date(
                                        "d M Y",
                                        strtotime($hearing['data'])
                                    );
                                    ?>
                                </span>

                            </div>

                        <?php } ?>

                    <?php } else { ?>

                        <p class="empty">
                            No upcoming hearings.
                        </p>

                    <?php } ?>

                </section>


                <!-- Pending Tasks -->

                <section class="section-card">

                    <div class="section-header">
                        <h3>Pending Tasks</h3>
                        <a href="tasks.php">View All</a>
                    </div>

                    <?php if ($tasks->num_rows > 0) { ?>

                        <?php while ($task = $tasks->fetch_assoc()) { ?>

                            <div class="list-item">

                                <div>
                                    <h4>
                                        <?php echo htmlspecialchars($task['title']); ?>
                                    </h4>

                                    <p>
                                        <?php echo htmlspecialchars($task['priority']); ?>
                                        Priority
                                    </p>
                                </div>

                                <span>
                                    <?php
                                    echo $task['deadline']
                                        ? date(
                                            "d M Y",
                                            strtotime($task['deadline'])
                                        )
                                        : "No deadline";
                                    ?>
                                </span>

                            </div>

                        <?php } ?>

                    <?php } else { ?>

                        <p class="empty">
                            No pending tasks.
                        </p>

                    <?php } ?>

                </section>

            </div>


            <!-- My Bookmarks -->

            <section class="section-card bookmarks-section">

                <div class="section-header">
                    <h3>My Bookmarks</h3>
                    <a href="research.php">Legal Research</a>
                </div>

                <?php if ($bookmarked_documents->num_rows > 0) { ?>

                    <div class="table-container">

                        <table>

                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Document Type</th>
                                    <th>Court</th>
                                    <th>Year</th>
                                    <th>Bookmarked On</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php while ($bookmark = $bookmarked_documents->fetch_assoc()) { ?>

                                    <tr>

                                        <td>
                                            <strong>
                                                <?php echo htmlspecialchars($bookmark['title']); ?>
                                            </strong>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($bookmark['document_type']); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($bookmark['court'] ?: '-'); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($bookmark['year'] ?: '-'); ?>
                                        </td>

                                        <td>
                                            <?php
                                            echo date(
                                                "d M Y",
                                                strtotime($bookmark['bookmarked_at'])
                                            );
                                            ?>
                                        </td>

                                        <td>
                                            <a
                                                href="legal-document.php?id=<?php echo $bookmark['legal_document_id']; ?>"
                                                class="bookmark-view-btn"
                                            >
                                                View
                                            </a>
                                        </td>

                                    </tr>

                                <?php } ?>

                            </tbody>

                        </table>

                    </div>

                <?php } else { ?>

                    <p class="empty">
                        You haven't bookmarked any legal documents yet.
                        Visit Legal Research to bookmark documents.
                    </p>

                <?php } ?>

            </section>


        </div>

    </main>

</div>

</body>
</html>
