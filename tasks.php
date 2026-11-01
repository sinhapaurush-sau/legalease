
<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Add task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_task'])) {

    $case_id = (int) $_POST['case_id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $priority = $_POST['priority'];
    $deadline = $_POST['deadline'];
    $assigned_to = (int) $_POST['assigned_to'];

    $valid_priorities = ['Low', 'Medium', 'High'];

    // Check case ownership and assigned user
    $check = $conn->prepare("
        SELECT cid FROM cases
        WHERE cid = ? AND owner_id = ?
    ");
    $check->bind_param("ii", $case_id, $user_id);
    $check->execute();

    $case_valid = $check->get_result()->num_rows > 0;

    $user_check = $conn->prepare("
        SELECT id FROM users WHERE id = ?
    ");
    $user_check->bind_param("i", $assigned_to);
    $user_check->execute();

    $user_valid = $user_check->get_result()->num_rows > 0;

    if (
        $case_valid &&
        $user_valid &&
        $title !== '' &&
        in_array($priority, $valid_priorities, true) &&
        $deadline !== ''
    ) {
        $stmt = $conn->prepare("
            INSERT INTO case_tasks
            (case_id, assigned_to, title, description, priority, status, deadline)
            VALUES (?, ?, ?, ?, ?, 'Pending', ?)
        ");

        $stmt->bind_param(
            "iissss",
            $case_id,
            $assigned_to,
            $title,
            $description,
            $priority,
            $deadline
        );

        $stmt->execute();

        header("Location: tasks.php");
        exit();
    } else {
        $error = "Please enter valid task details.";
    }
}

// Update task status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {

    $task_id = (int) $_POST['task_id'];
    $new_status = $_POST['new_status'];

    $valid_statuses = ['Pending', 'In Progress', 'Completed'];

    if (in_array($new_status, $valid_statuses, true)) {

        $stmt = $conn->prepare("
            UPDATE case_tasks
            INNER JOIN cases ON case_tasks.case_id = cases.cid
            SET case_tasks.status = ?
            WHERE case_tasks.task_id = ?
            AND cases.owner_id = ?
        ");

        $stmt->bind_param("sii", $new_status, $task_id, $user_id);
        $stmt->execute();
    }

    header("Location: tasks.php");
    exit();
}

// Fetch cases for dropdown
$case_stmt = $conn->prepare("
    SELECT cid, applicant, defendent
    FROM cases
    WHERE owner_id = ?
    ORDER BY cid DESC
");
$case_stmt->bind_param("i", $user_id);
$case_stmt->execute();
$cases = $case_stmt->get_result();

// Fetch users for assignment
$users = $conn->query("SELECT id, name FROM users ORDER BY name ASC");

// Search and filter
$search = trim($_GET['search'] ?? '');
$status_filter = $_GET['status'] ?? '';

$sql = "
    SELECT
        case_tasks.*,
        cases.applicant,
        cases.defendent,
        users.name AS assigned_name
    FROM case_tasks
    INNER JOIN cases ON case_tasks.case_id = cases.cid
    INNER JOIN users ON case_tasks.assigned_to = users.id
    WHERE cases.owner_id = ?
";

$params = [$user_id];
$types = "i";

if ($search !== '') {
    $sql .= " AND (
        case_tasks.title LIKE ?
        OR case_tasks.description LIKE ?
        OR cases.applicant LIKE ?
        OR cases.defendent LIKE ?
    )";

    $term = "%" . $search . "%";

    $params[] = $term;
    $params[] = $term;
    $params[] = $term;
    $params[] = $term;

    $types .= "ssss";
}

if ($status_filter !== '') {
    $sql .= " AND case_tasks.status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

$sql .= " ORDER BY case_tasks.deadline ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$tasks = $stmt->get_result();

// Task counts
$count_stmt = $conn->prepare("
    SELECT
        COUNT(*) AS total,
        SUM(case_tasks.status = 'Pending') AS pending,
        SUM(case_tasks.status = 'In Progress') AS in_progress,
        SUM(case_tasks.status = 'Completed') AS completed
    FROM case_tasks
    INNER JOIN cases ON case_tasks.case_id = cases.cid
    WHERE cases.owner_id = ?
");
$count_stmt->bind_param("i", $user_id);
$count_stmt->execute();
$counts = $count_stmt->get_result()->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasks - LegalEase</title>
    <link rel="stylesheet" href="css/tasks.css">
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
        <a href="hearings.php">Hearings</a>
        <a href="tasks.php" class="active">Tasks & Notes</a>
    </div>

    <div class="sidebar-bottom">
        <a href="profile.php">My Profile</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="main-content">

    <div class="topbar">
        <h2>Tasks & Notes</h2>
        <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
    </div>

    <div class="page-content">

        <div class="page-heading">
            <div>
                <h2>Task Management</h2>
                <p>Create and manage tasks related to your cases.</p>
            </div>
        </div>

        <!-- Task Statistics -->

        <div class="stats-grid">

            <div class="stat-card">
                <p>Total Tasks</p>
                <h2><?php echo $counts['total'] ?? 0; ?></h2>
            </div>

            <div class="stat-card">
                <p>Pending</p>
                <h2><?php echo $counts['pending'] ?? 0; ?></h2>
            </div>

            <div class="stat-card">
                <p>In Progress</p>
                <h2><?php echo $counts['in_progress'] ?? 0; ?></h2>
            </div>

            <div class="stat-card">
                <p>Completed</p>
                <h2><?php echo $counts['completed'] ?? 0; ?></h2>
            </div>

        </div>

        <?php if (isset($error)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Add Task -->

        <div class="form-card">

            <h3>Add New Task</h3>

            <form method="POST">

                <div class="form-grid">

                    <div class="form-group">
                        <label>Task Title</label>
                        <input
                            type="text"
                            name="title"
                            placeholder="Enter task title"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label>Related Case</label>

                        <select name="case_id" required>
                            <option value="">Select Case</option>

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
                        <label>Assign To</label>

                        <select name="assigned_to" required>
                            <option value="">Select User</option>

                            <?php while ($user = $users->fetch_assoc()): ?>
                                <option value="<?php echo $user['id']; ?>">
                                    <?php echo htmlspecialchars($user['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Priority</label>

                        <select name="priority" required>
                            <option value="Low">Low</option>
                            <option value="Medium" selected>Medium</option>
                            <option value="High">High</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Deadline</label>
                        <input type="datetime-local" name="deadline" required>
                    </div>

                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea
                        name="description"
                        rows="3"
                        placeholder="Enter task description..."
                    ></textarea>
                </div>

                <button type="submit" name="add_task" class="submit-btn">
                    Add Task
                </button>

            </form>

        </div>

        <!-- Filters -->

        <div class="filter-card">

            <form method="GET" class="filter-form">

                <input
                    type="text"
                    name="search"
                    placeholder="Search tasks or cases..."
                    value="<?php echo htmlspecialchars($search); ?>"
                >

                <select name="status">
                    <option value="">All Statuses</option>

                    <?php foreach (['Pending', 'In Progress', 'Completed'] as $status): ?>
                        <option
                            value="<?php echo $status; ?>"
                            <?php echo $status_filter === $status ? 'selected' : ''; ?>
                        >
                            <?php echo $status; ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit">Filter</button>
                <a href="tasks.php" class="reset-btn">Reset</a>

            </form>

        </div>

        <!-- Task Table -->

        <div class="table-card">

            <h3>All Tasks</h3>

            <div class="table-container">

                <table>
                    <thead>
                        <tr>
                            <th>Task</th>
                            <th>Related Case</th>
                            <th>Assigned To</th>
                            <th>Priority</th>
                            <th>Deadline</th>
                            <th>Status</th>
                            <th>Update Status</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php if ($tasks->num_rows > 0): ?>

                            <?php while ($task = $tasks->fetch_assoc()): ?>

                                <tr>
                                    <td>
                                        <strong>
                                            <?php echo htmlspecialchars($task['title']); ?>
                                        </strong>
                                        <p class="description">
                                            <?php echo htmlspecialchars($task['description']); ?>
                                        </p>
                                    </td>

                                    <td>
                                        <a href="case-details.php?id=<?php echo $task['case_id']; ?>">
                                            <?php echo htmlspecialchars($task['applicant']); ?>
                                            vs
                                            <?php echo htmlspecialchars($task['defendent']); ?>
                                        </a>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($task['assigned_name']); ?>
                                    </td>

                                    <td>
                                        <span class="priority <?php echo strtolower($task['priority']); ?>">
                                            <?php echo htmlspecialchars($task['priority']); ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?php echo date("d M Y, h:i A", strtotime($task['deadline'])); ?>
                                    </td>

                                    <td>
                                        <span class="status <?php echo strtolower(str_replace(' ', '-', $task['status'])); ?>">
                                            <?php echo htmlspecialchars($task['status']); ?>
                                        </span>
                                    </td>

                                    <td>
                                        <form method="POST" class="status-form">

                                            <input
                                                type="hidden"
                                                name="task_id"
                                                value="<?php echo $task['task_id']; ?>"
                                            >

                                            <select name="new_status">
                                                <?php foreach (['Pending', 'In Progress', 'Completed'] as $status): ?>
                                                    <option
                                                        value="<?php echo $status; ?>"
                                                        <?php echo $task['status'] === $status ? 'selected' : ''; ?>
                                                    >
                                                        <?php echo $status; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>

                                            <button type="submit" name="update_status">
                                                Save
                                            </button>

                                        </form>
                                    </td>
                                </tr>

                            <?php endwhile; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="7" class="no-data">
                                    No tasks found.
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
