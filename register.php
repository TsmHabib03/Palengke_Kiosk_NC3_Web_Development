<?php
require_once 'config.php';

$error = "";
$success = "";
$selectedRole = (isset($_POST['role']) && $_POST['role'] === 'admin') ? 'admin' : 'customer';

// If already logged in, redirect
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: seller.php");
    } else {
        header("Location: buyer.php");
    }
    exit();
}

// Handle registration
if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $selectedRole = ($role === 'admin') ? 'admin' : 'customer';

    if (strlen($username) < 3) {
        $error = "Username must be at least 3 characters.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (!in_array($role, ['admin', 'customer'])) {
        $error = "Invalid role selected.";
    } else {
        $check = mysqli_query($conn, "SELECT id FROM users WHERE username='$username'");
        if (mysqli_num_rows($check) > 0) {
            $error = "Username already exists.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert = mysqli_query($conn, "INSERT INTO users (username, password, role) VALUES ('$username', '$hashed_password', '$role')");

            if ($insert) {
                $success = "Registration successful. You can now sign in.";
                $selectedRole = 'customer';
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
    <meta name="description" content="Create an account on Palengke Kiosk - Campus Marketplace">
    <title>Create Account - Palengke Kiosk</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body class="auth-page">

    <?php include 'partials/header.php'; ?>

    <main class="auth-layout">
        <article class="auth-card" aria-labelledby="register-heading">
            <header class="auth-header">
                <h2 id="register-heading">Create your account</h2>
                <p class="text-muted">Fill in your details to get started</p>
            </header>

            <?php if ($error): ?>
                <div class="alert alert-error" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success" role="alert">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="auth-form" aria-label="Registration form">
                <!-- Role Selection -->
                <fieldset class="field">
                    <legend class="label">I want to join as</legend>
                    <div class="role-grid">
                        <label class="role-option">
                            <input 
                                type="radio" 
                                name="role" 
                                value="customer" 
                                <?= $selectedRole === 'customer' ? 'checked' : '' ?>
                                required>
                            <span class="role-box">
                                <strong>Customer</strong>
                                <small>Browse and buy items</small>
                            </span>
                        </label>

                        <label class="role-option">
                            <input 
                                type="radio" 
                                name="role" 
                                value="admin" 
                                <?= $selectedRole === 'admin' ? 'checked' : '' ?>
                                required>
                            <span class="role-box">
                                <strong>Seller</strong>
                                <small>Post and manage products</small>
                            </span>
                        </label>
                    </div>
                </fieldset>

                <!-- Username -->
                <div class="field">
                    <label class="label" for="username">Username</label>
                    <input 
                        class="input" 
                        type="text" 
                        id="username" 
                        name="username" 
                        placeholder="Choose a username"
                        required 
                        minlength="3"
                        autocomplete="username"
                        value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
                </div>

                <!-- Password -->
                <div class="field">
                    <label class="label" for="password">Password</label>
                    <input 
                        class="input" 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Create a password (min 6 characters)"
                        required 
                        minlength="6"
                        autocomplete="new-password">
                </div>

                <!-- Confirm Password -->
                <div class="field">
                    <label class="label" for="confirm_password">Confirm Password</label>
                    <input 
                        class="input" 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        placeholder="Confirm your password"
                        required
                        autocomplete="new-password">
                </div>

                <button type="submit" name="register" class="btn btn-primary btn-lg auth-submit">
                    Create Account
                </button>
            </form>

            <footer class="auth-footer">
                <p class="auth-switch">
                    Already have an account? <a href="login.php">Sign in</a>
                </p>
            </footer>
        </article>
    </main>

    <?php include 'partials/footer.php'; ?>

</body>
</html>
