<?php
session_start();
require_once 'includes/db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE name = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];

            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LegalEase</title>
    <link rel="stylesheet" href="css/login.css">
</head>

<body>

    <div class="login-container">

        <div class="logo">
            <h2>LegalEase</h2>
            <p>Legal Research & Case Management</p>
        </div>

        <div class="login-card">
            <h3>Welcome Back</h3>
            <p class="subtitle">Login to your account</p>

            <?php if (!empty($error)) { ?>
                <div class="error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php } ?>

            <form method="POST" action="">

                <div class="form-group">
                    <label>Username</label>
                    <input
                        type="text"
                        name="username"
                        placeholder="Enter your username"
                        required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input
                        type="password"
                        name="password"
                        placeholder="Enter your password"
                        required>
                </div>

                <button type="submit">Login</button>

            </form>

            <div class="register-link">
                Don't have an account?
                <a href="register.php">Register here</a>
            </div>
        </div>
    </div>

</body>

</html>