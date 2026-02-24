<?php
require_once 'config.php';

$error = "";

// If already logged in, redirect based on role
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: seller.php");
    } else {
        header("Location: buyer.php");
    }
    exit();
}

// Handle login
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");

    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];

            if ($row['role'] === 'admin') {
                header("Location: seller.php");
            } else {
                header("Location: buyer.php");
            }
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found.";
    }
}

if (isset($_GET['error']) && $_GET['error'] === 'unauthorized') {
    $error = "You do not have permission to access that page.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sign in to Palengke Kiosk - Campus Marketplace">
    <title>Sign In - Palengke Kiosk</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body class="auth-page">

    <?php include 'partials/header.php'; ?>

    <main class="auth-layout">
        <article class="auth-card" aria-labelledby="login-heading">
            <header class="auth-header">
                <h1 id="login-heading">Welcome Back</h1>
                <p class="text-muted">Sign in to access your account</p>
            </header>

            <?php if ($error): ?>
                <div class="alert alert-error" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="auth-form" aria-label="Login form">
                <div class="field">
                    <label class="label" for="username">Username</label>
                    <input 
                        class="input" 
                        type="text" 
                        id="username" 
                        name="username" 
                        placeholder="Enter your username"
                        required 
                        autofocus
                        autocomplete="username">
                </div>

                <div class="field">
                    <label class="label" for="password">Password</label>
                    <input 
                        class="input" 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Enter your password"
                        required
                        autocomplete="current-password">
                </div>

                <button type="submit" name="login" class="btn btn-primary btn-lg auth-submit">
                    Sign In
                </button>
            </form>

            <footer class="auth-footer">
                <p class="auth-switch">
                    New here? <a href="register.php">Create an account</a>
                </p>
            </footer>
        </article>
    </main>

    <?php include 'partials/footer.php'; ?>

</body>
</html>
