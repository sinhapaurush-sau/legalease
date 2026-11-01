
<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Search and filter
$search = trim($_GET['search'] ?? '');
$status = trim($_GET['status'] ?? '');

$sql = "
    SELECT cases.*, client.name AS client_name
    FROM cases
    INNER JOIN client ON cases.client_id = client.client_id
    WHERE cases.owner_id = ?
";

if ($search != '') {
    $sql .= " AND (
        cases.applicant LIKE ?
        OR cases.defendent LIKE ?
        OR client.name LIKE ?
    )";
}

if ($status != '') {
    $sql .= " AND cases.status = ?";
}

$sql .= " ORDER BY cases.created_at DESC";

$stmt = $conn->prepare($sql);

if ($search != '' && $status != '') {
    $keyword = "%$search%";
    $stmt->bind_param("issss", $user_id, $keyword, $keyword, $keyword, $status);
} elseif ($search != '') {
    $keyword = "%$search%";
    $stmt->bind_param("isss", $user_id, $keyword, $keyword, $keyword);
} elseif ($status != '') {
    $stmt->bind_param("is", $user_id, $status);
} else {
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$cases = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cases - LegalEase</title>
    <link rel="stylesheet" href="css/cases.css">
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
                <h2>Cases</h2>
                <p>Manage your legal cases</p>
            </div>

            <div class="user-info">
                <?php echo htmlspecialchars($_SESSION['user_name']); ?>
            </div>
        </header>

        <div class="content">

            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h3>All Cases</h3>
                    <p>View and manage your registered cases.</p>
                </div>

                <a href="case-form.php" class="add-btn">+ Add Case</a>
            </div>

            <!-- Search and Filter -->
            <div class="filter-box">

                <form method="GET">

                    <input
                        type="text"
                        name="search"
                        placeholder="Search by applicant, defendant or client..."
                        value="<?php echo htmlspecialchars($search); ?>"
                    >

                    <select name="status">
                        <option value="">All Status</option>
                        <option value="Pending" <?php echo $status == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="Ongoing" <?php echo $status == 'Ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                        <option value="Closed" <?php echo $status == 'Closed' ? 'selected' : ''; ?>>Closed</option>
                        <option value="Appealed" <?php echo $status == 'Appealed' ? 'selected' : ''; ?>>Appealed</option>
                    </select>

                    <button type="submit">Apply</button>

                    <?php if ($search != '' || $status != '') { ?>
                        <a href="cases.php" class="reset-btn">Clear</a>
                    <?php } ?>

                </form>

            </div>

            <!-- Cases Table -->
            <div class="table-container">

                <table>
                    <thead>
                        <tr>
                            <th>Case ID</th>
                            <th>Applicant</th>
                            <th>Defendant</th>
                            <th>Client</th>
                            <th>Status</th>
                            <th>Created On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php if ($cases->num_rows > 0) { ?>

                            <?php while ($case = $cases->fetch_assoc()) { ?>

                                <tr>
                                    <td>#<?php echo $case['cid']; ?></td>

                                    <td>
                                        <?php echo htmlspecialchars($case['applicant']); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($case['defendent']); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($case['client_name']); ?>
                                    </td>

                                    <td>
                                        <span class="status">
                                            <?php echo htmlspecialchars($case['status']); ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?php echo date("d M Y", strtotime($case['created_at'])); ?>
                                    </td>

                                    <td class="actions">

                                        <a
                                            href="case-details.php?id=<?php echo $case['cid']; ?>"
                                            class="view-btn"
                                        >
                                            View
                                        </a>

                                        <a
                                            href="case-form.php?id=<?php echo $case['cid']; ?>"
                                            class="edit-btn"
                                        >
                                            Edit
                                        </a>

                                        <a
                                            href="case-delete.php?id=<?php echo $case['cid']; ?>"
                                            class="delete-btn"
                                            onclick="return confirm('Are you sure you want to delete this case?')"
                                        >
                                            Delete
                                        </a>

                                    </td>
                                </tr>

                            <?php } ?>

                        <?php } else { ?>

                            <tr>
                                <td colspan="7" class="empty">
                                    No cases found.
                                </td>
                            </tr>

                        <?php } ?>

                    </tbody>
                </table>

            </div>

        </div>

    </main>

</div>

</body>
</html>
