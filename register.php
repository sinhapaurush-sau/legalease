
<?php
session_start();
require_once 'includes/db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($name === '' || $password === '' || $confirm_password === '') {
        $error = "Please fill all the fields.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must contain at least 6 characters.";
    } else {

        // Check whether username already exists
        $check = $conn->prepare("SELECT id FROM users WHERE name = ?");
        $check->bind_param("s", $name);
        $check->execute();

        if ($check->get_result()->num_rows > 0) {
            $error = "This username is already registered.";
        } else {

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $authkey = "";

            $stmt = $conn->prepare("
                INSERT INTO users (name, password, authkey)
                VALUES (?, ?, ?)
            ");

            $stmt->bind_param("sss", $name, $hashed_password, $authkey);

            if ($stmt->execute()) {
                header("Location: login.php?registered=1");
                exit();
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - LegalEase</title>
    <link rel="stylesheet" href="css/register.css">
</head>
<body>

<div class="register-container">

    <div class="logo">
        <h1>LegalEase</h1>
        <p>Create your account</p>
    </div>

    <?php if ($error !== ''): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <div class="form-group">
            <label>Username</label>
            <input
                type="text"
                name="name"
                placeholder="Enter your username"
                value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                required
            >
        </div>

        <div class="form-group">
            <label>Password</label>
            <input
                type="password"
                name="password"
                placeholder="Enter your password"
                required
            >
        </div>

        <div class="form-group">
            <label>Confirm Password</label>
            <input
                type="password"
                name="confirm_password"
                placeholder="Confirm your password"
                required
            >
        </div>

        <button type="submit">Create Account</button>

    </form>

    <div class="login-link">
        Already have an account?
        <a href="login.php">Login here</a>
    </div>

</div>

</body>
</html>
