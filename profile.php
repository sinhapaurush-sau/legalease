
<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";
$error = "";

// Fetch current user details
$stmt = $conn->prepare("SELECT id, name, password FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Update profile
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {

    $name = trim($_POST['name']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($name === '') {
        $error = "Name cannot be empty.";
    } elseif (!password_verify($current_password, $user['password'])) {
        $error = "Current password is incorrect.";
    } elseif ($new_password !== '' && $new_password !== $confirm_password) {
        $error = "New passwords do not match.";
    } else {

        if ($new_password !== '') {

            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            $update = $conn->prepare("
                UPDATE users SET name = ?, password = ?
                WHERE id = ?
            ");

            $update->bind_param("ssi", $name, $hashed_password, $user_id);

        } else {

            $update = $conn->prepare("
                UPDATE users SET name = ?
                WHERE id = ?
            ");

            $update->bind_param("si", $name, $user_id);
        }

        if ($update->execute()) {
            $_SESSION['user_name'] = $name;

            header("Location: profile.php?updated=1");
            exit();
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}

if (isset($_GET['updated'])) {
    $message = "Profile updated successfully.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - LegalEase</title>
    <link rel="stylesheet" href="css/profile.css">
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
        <a href="tasks.php">Tasks & Notes</a>
    </div>

    <div class="sidebar-bottom">
        <a href="profile.php" class="active">My Profile</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="main-content">

    <div class="topbar">
        <h2>My Profile</h2>
        <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
    </div>

    <div class="page-content">

        <div class="page-heading">
            <h2>Account Settings</h2>
            <p>View and update your account information.</p>
        </div>

        <?php if ($message): ?>
            <div class="success-message">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="profile-card">

            <div class="profile-header">
                <div class="profile-avatar">
                    <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                </div>

                <div>
                    <h3><?php echo htmlspecialchars($user['name']); ?></h3>
                    <p>LegalEase User</p>
                </div>
            </div>

            <div class="account-info">
                <div>
                    <label>User ID</label>
                    <p>#<?php echo $user['id']; ?></p>
                </div>

                <div>
                    <label>Account Name</label>
                    <p><?php echo htmlspecialchars($user['name']); ?></p>
                </div>
            </div>

            <div class="divider"></div>

            <h3 class="form-heading">Edit Profile</h3>

            <form method="POST">

                <div class="form-group">
                    <label>Name</label>
                    <input
                        type="text"
                        name="name"
                        value="<?php echo htmlspecialchars($user['name']); ?>"
                        required
                    >
                </div>

                <div class="divider"></div>

                <h3 class="form-heading">Change Password</h3>

                <p class="form-description">
                    Leave the new password fields empty if you don't want to change your password.
                </p>

                <div class="form-group">
                    <label>Current Password</label>
                    <input
                        type="password"
                        name="current_password"
                        placeholder="Enter current password"
                        required
                    >
                </div>

                <div class="form-group">
                    <label>New Password</label>
                    <input
                        type="password"
                        name="new_password"
                        placeholder="Enter new password"
                    >
                </div>

                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input
                        type="password"
                        name="confirm_password"
                        placeholder="Confirm new password"
                    >
                </div>

                <button type="submit" name="update_profile" class="submit-btn">
                    Save Changes
                </button>

            </form>

        </div>

    </div>

</div>

</body>
</html>
