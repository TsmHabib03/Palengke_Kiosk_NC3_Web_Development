<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* DATABASE CONNECTION */
$conn = mysqli_connect("localhost", "root", "muning0328", "palengke_kiosk");
if (!$conn) die("Database Error");

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to check user role
function getUserRole() {
    return $_SESSION['role'] ?? null;
}

// Function to require login
function requireLogin($role = null) {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
    if ($role && getUserRole() !== $role) {
        header("Location: login.php?error=unauthorized");
        exit();
    }
}
?>
