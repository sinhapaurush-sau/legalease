
<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$case_id = $_GET['id'] ?? null;

$applicant = "";
$defendent = "";
$body = "";
$client_id = "";
$status = "Pending";
$judge = "";
$error = "";

// Fetch clients for dropdown
$clients = $conn->query("SELECT client_id, name FROM client ORDER BY name ASC");

// Fetch case details when editing
if ($case_id) {

    $stmt = $conn->prepare("
        SELECT * FROM cases
        WHERE cid = ? AND owner_id = ?
    ");

    $stmt->bind_param("ii", $case_id, $user_id);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        die("Case not found.");
    }

    $case = $result->fetch_assoc();

    $applicant = $case['applicant'];
    $defendent = $case['defendent'];
    $body = $case['body'];
    $client_id = $case['client_id'];
    $status = $case['status'];
    $judge = $case['judge'];
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $applicant = trim($_POST['applicant']);
    $defendent = trim($_POST['defendent']);
    $body = trim($_POST['body']);
    $client_id = (int) $_POST['client_id'];
    $status = $_POST['status'];
    $judge = trim($_POST['judge']);

    $allowed_statuses = ['Pending', 'Ongoing', 'Closed', 'Appealed'];

    // Check whether selected client exists
    $client_check = $conn->prepare("
        SELECT client_id FROM client WHERE client_id = ?
    ");

    $client_check->bind_param("i", $client_id);
    $client_check->execute();

    $client_exists = $client_check->get_result()->num_rows > 0;

    if ($applicant == "" || $defendent == "" || $client_id <= 0) {
        $error = "Please fill all required fields.";
    } elseif (!$client_exists) {
        $error = "Please select a valid client.";
    } elseif (!in_array($status, $allowed_statuses, true)) {
        $error = "Invalid case status.";
    } else {

        if ($case_id) {

            // Update case
            $stmt = $conn->prepare("
                UPDATE cases
                SET applicant = ?,
                    defendent = ?,
                    body = ?,
                    client_id = ?,
                    status = ?,
                    judge = ?
                WHERE cid = ? AND owner_id = ?
            ");

            $stmt->bind_param(
                "sssissii",
                $applicant,
                $defendent,
                $body,
                $client_id,
                $status,
                $judge,
                $case_id,
                $user_id
            );

        } else {

            // Insert new case
            $stmt = $conn->prepare("
                INSERT INTO cases
                (applicant, defendent, body, client_id, status, judge, owner_id)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->bind_param(
                "sssissi",
                $applicant,
                $defendent,
                $body,
                $client_id,
                $status,
                $judge,
                $user_id
            );
        }

        if ($stmt->execute()) {
            header("Location: cases.php");
            exit();
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}

$is_edit = !empty($case_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_edit ? 'Edit Case' : 'Add Case'; ?> - LegalEase</title>
    <link rel="stylesheet" href="css/case-form.css">
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
                <h2><?php echo $is_edit ? 'Edit Case' : 'Add Case'; ?></h2>
                <p>Manage legal case information</p>
            </div>

            <div class="user-info">
                <?php echo htmlspecialchars($_SESSION['user_name']); ?>
            </div>
        </header>

        <div class="content">

            <div class="page-header">
                <div>
                    <h3><?php echo $is_edit ? 'Update Case' : 'New Case'; ?></h3>
                    <p>Enter the case details below.</p>
                </div>

                <a href="cases.php" class="back-btn">Back to Cases</a>
            </div>

            <div class="form-card">

                <?php if ($error != "") { ?>
                    <div class="error">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php } ?>

                <form method="POST">

                    <div class="form-group">
                        <label>Applicant Name *</label>
                        <input
                            type="text"
                            name="applicant"
                            placeholder="Enter applicant name"
                            value="<?php echo htmlspecialchars($applicant); ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label>Defendant Name *</label>
                        <input
                            type="text"
                            name="defendent"
                            placeholder="Enter defendant name"
                            value="<?php echo htmlspecialchars($defendent); ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label>Select Client *</label>

                        <select name="client_id" required>
                            <option value="">Select a client</option>

                            <?php while ($client = $clients->fetch_assoc()) { ?>

                                <option
                                    value="<?php echo $client['client_id']; ?>"
                                    <?php echo $client_id == $client['client_id'] ? 'selected' : ''; ?>
                                >
                                    <?php echo htmlspecialchars($client['name']); ?>
                                </option>

                            <?php } ?>

                        </select>
                    </div>

                    <div class="form-group">
                        <label>Judge Name</label>
                        <input
                            type="text"
                            name="judge"
                            placeholder="Enter judge name"
                            value="<?php echo htmlspecialchars($judge); ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label>Case Status</label>

                        <select name="status">
                            <option value="Pending" <?php echo $status == 'Pending' ? 'selected' : ''; ?>>
                                Pending
                            </option>

                            <option value="Ongoing" <?php echo $status == 'Ongoing' ? 'selected' : ''; ?>>
                                Ongoing
                            </option>

                            <option value="Closed" <?php echo $status == 'Closed' ? 'selected' : ''; ?>>
                                Closed
                            </option>

                            <option value="Appealed" <?php echo $status == 'Appealed' ? 'selected' : ''; ?>>
                                Appealed
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Case Description</label>
                        <textarea
                            name="body"
                            rows="5"
                            placeholder="Enter case details..."
                        ><?php echo htmlspecialchars($body); ?></textarea>
                    </div>

                    <div class="form-actions">

                        <button type="submit">
                            <?php echo $is_edit ? 'Update Case' : 'Save Case'; ?>
                        </button>

                        <a href="cases.php" class="cancel-btn">Cancel</a>

                    </div>

                </form>

            </div>

        </div>

    </main>

</div>

</body>
</html>
