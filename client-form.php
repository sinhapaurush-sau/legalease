
<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$client_id = $_GET['id'] ?? null;

$name = "";
$phone = "";
$note = "";
$error = "";

// Fetch client details when editing
if ($client_id) {
    $stmt = $conn->prepare("SELECT * FROM client WHERE client_id = ?");
    $stmt->bind_param("i", $client_id);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        die("Client not found.");
    }

    $client = $result->fetch_assoc();

    $name = $client['name'];
    $phone = $client['phone'];
    $note = $client['note'];
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $note = trim($_POST['note']);

    if ($name == "" || $phone == "") {
        $error = "Name and phone number are required.";
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $error = "Please enter a valid 10-digit phone number.";
    } else {

        if ($client_id) {

            // Update client
            $stmt = $conn->prepare("
                UPDATE client
                SET name = ?, phone = ?, note = ?
                WHERE client_id = ?
            ");

            $stmt->bind_param("sssi", $name, $phone, $note, $client_id);

        } else {

            // Insert new client
            $stmt = $conn->prepare("
                INSERT INTO client (name, phone, note)
                VALUES (?, ?, ?)
            ");

            $stmt->bind_param("sss", $name, $phone, $note);
        }

        if ($stmt->execute()) {
            header("Location: clients.php");
            exit();
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}

$is_edit = !empty($client_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_edit ? 'Edit Client' : 'Add Client'; ?> - LegalEase</title>
    <link rel="stylesheet" href="css/client-form.css">
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
            <a href="clients.php" class="active">Clients</a>
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
                <h2><?php echo $is_edit ? 'Edit Client' : 'Add Client'; ?></h2>
                <p>Manage client information</p>
            </div>

            <div class="user-info">
                <?php echo htmlspecialchars($_SESSION['user_name']); ?>
            </div>
        </header>

        <div class="content">

            <div class="page-header">
                <div>
                    <h3><?php echo $is_edit ? 'Update Client' : 'New Client'; ?></h3>
                    <p>Enter the client's details below.</p>
                </div>

                <a href="clients.php" class="back-btn">Back to Clients</a>
            </div>

            <div class="form-card">

                <?php if ($error != "") { ?>
                    <div class="error">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php } ?>

                <form method="POST">

                    <div class="form-group">
                        <label>Client Name *</label>
                        <input
                            type="text"
                            name="name"
                            placeholder="Enter client name"
                            value="<?php echo htmlspecialchars($name); ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label>Phone Number *</label>
                        <input
                            type="text"
                            name="phone"
                            placeholder="Enter 10-digit phone number"
                            maxlength="10"
                            value="<?php echo htmlspecialchars($phone); ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label>Notes</label>
                        <textarea
                            name="note"
                            rows="5"
                            placeholder="Enter additional information about the client..."
                        ><?php echo htmlspecialchars($note); ?></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit">
                            <?php echo $is_edit ? 'Update Client' : 'Save Client'; ?>
                        </button>

                        <a href="clients.php" class="cancel-btn">Cancel</a>
                    </div>

                </form>

            </div>

        </div>

    </main>

</div>

</body>
</html>
