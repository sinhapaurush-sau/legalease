
<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Add hearing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_hearing'])) {
    $case_id = (int) $_POST['case_id'];
    $hearing_date = $_POST['hearing_date'];
    $status = $_POST['status'];
    $notes = trim($_POST['notes']);
    $conclusion = trim($_POST['conclusion']);

    // Check whether the case belongs to the logged-in user
    $check = $conn->prepare("
        SELECT cid FROM cases
        WHERE cid = ? AND owner_id = ?
    ");
    $check->bind_param("ii", $case_id, $user_id);
    $check->execute();
    $case_result = $check->get_result();

    if ($case_result->num_rows > 0 && $hearing_date !== '') {
        $stmt = $conn->prepare("
            INSERT INTO hearing (data, conclusion, notes, status, c_id)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "ssssi",
            $hearing_date,
            $conclusion,
            $notes,
            $status,
            $case_id
        );

        $stmt->execute();

        header("Location: hearings.php");
        exit();
    } else {
        $error = "Please select a valid case and hearing date.";
    }
}

// Fetch user's cases for dropdown
$case_stmt = $conn->prepare("
    SELECT cid, applicant, defendent
    FROM cases
    WHERE owner_id = ?
    ORDER BY cid DESC
");
$case_stmt->bind_param("i", $user_id);
$case_stmt->execute();
$cases = $case_stmt->get_result();

// Search and status filter
$search = trim($_GET['search'] ?? '');
$status_filter = $_GET['status'] ?? '';

$sql = "
    SELECT hearing.*, cases.applicant, cases.defendent, cases.cid
    FROM hearing
    INNER JOIN cases ON hearing.c_id = cases.cid
    WHERE cases.owner_id = ?
";

$params = [$user_id];
$types = "i";

if ($search !== '') {
    $sql .= " AND (
        cases.applicant LIKE ?
        OR cases.defendent LIKE ?
        OR hearing.conclusion LIKE ?
    )";

    $term = "%" . $search . "%";

    $params[] = $term;
    $params[] = $term;
    $params[] = $term;

    $types .= "sss";
}

if ($status_filter !== '') {
    $sql .= " AND hearing.status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

$sql .= " ORDER BY hearing.data DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$hearings = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hearings - LegalEase</title>
    <link rel="stylesheet" href="css/hearings.css">
</head>
<body>

<div class="sidebar">
    <h2>LegalEase</h2>

    <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="clients.php">Clients</a>
        <a href="cases.php">Cases</a>
        <a href="research.php">Legal Research</a>
        <a href="bookmarks.php">Documents</a>
        <a href="hearings.php" class="active">Hearings</a>
        <a href="tasks.php">Tasks & Notes</a>
    </div>

    <div class="sidebar-bottom">
        <a href="profile.php">My Profile</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="main-content">

    <div class="topbar">
        <h2>Hearings</h2>
        <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
    </div>

    <div class="page-content">

        <div class="page-heading">
            <div>
                <h2>Hearing Management</h2>
                <p>View and manage court hearing records.</p>
            </div>

            <span class="count">
                <?php echo $hearings->num_rows; ?> Hearings
            </span>
        </div>

        <?php if (isset($error)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="form-card">
            <h3>Add New Hearing</h3>

            <form method="POST">

                <div class="form-grid">

                    <div class="form-group">
                        <label>Select Case</label>

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
                        <label>Hearing Date & Time</label>
                        <input type="datetime-local" name="hearing_date" required>
                    </div>

                    <div class="form-group">
                        <label>Status</label>

                        <select name="status" required>
                            <option value="Scheduled">Scheduled</option>
                            <option value="Completed">Completed</option>
                            <option value="Adjourned">Adjourned</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Conclusion</label>
                        <input type="text" name="conclusion" placeholder="Enter conclusion">
                    </div>

                </div>

                <div class="form-group">
                    <label>Hearing Notes</label>
                    <textarea name="notes" rows="3" placeholder="Enter hearing notes..."></textarea>
                </div>

                <button type="submit" name="add_hearing" class="submit-btn">
                    Add Hearing
                </button>

            </form>
        </div>

        <div class="filter-card">

            <form method="GET" class="filter-form">

                <input
                    type="text"
                    name="search"
                    placeholder="Search by applicant, defendant or conclusion..."
                    value="<?php echo htmlspecialchars($search); ?>"
                >

                <select name="status">
                    <option value="">All Statuses</option>
                    <?php
                    $statuses = ['Scheduled', 'Completed', 'Adjourned', 'Cancelled'];

                    foreach ($statuses as $status):
                    ?>
                        <option
                            value="<?php echo $status; ?>"
                            <?php echo $status_filter === $status ? 'selected' : ''; ?>
                        >
                            <?php echo $status; ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit">Filter</button>

                <a href="hearings.php" class="reset-btn">Reset</a>

            </form>

        </div>

        <div class="table-card">

            <h3>Hearing Records</h3>

            <div class="table-container">

                <table>
                    <thead>
                        <tr>
                            <th>Hearing ID</th>
                            <th>Case</th>
                            <th>Hearing Date</th>
                            <th>Status</th>
                            <th>Conclusion</th>
                            <th>Notes</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php if ($hearings->num_rows > 0): ?>

                            <?php while ($hearing = $hearings->fetch_assoc()): ?>

                                <tr>
                                    <td>#<?php echo $hearing['h_id']; ?></td>

                                    <td>
                                        <a href="case-details.php?id=<?php echo $hearing['cid']; ?>">
                                            <?php echo htmlspecialchars($hearing['applicant']); ?>
                                            vs
                                            <?php echo htmlspecialchars($hearing['defendent']); ?>
                                        </a>
                                    </td>

                                    <td>
                                        <?php echo date("d M Y, h:i A", strtotime($hearing['data'])); ?>
                                    </td>

                                    <td>
                                        <span class="status <?php echo strtolower($hearing['status']); ?>">
                                            <?php echo htmlspecialchars($hearing['status']); ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($hearing['conclusion'] ?: '-'); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($hearing['notes'] ?: '-'); ?>
                                    </td>
                                </tr>

                            <?php endwhile; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="6" class="no-data">
                                    No hearing records found.
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
