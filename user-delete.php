<?php
session_start();
include 'includes/db.php';

// Security checks
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['finance', 'admin'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid user ID.";
    header("Location: manage-users.php");
    exit;
}

$id = (int)$_GET['id'];

// Optional: Prevent deleting yourself
if ($id == $_SESSION['user_id']) {
    $_SESSION['error'] = "You cannot delete your own account.";
    header("Location: manage-users.php");
    exit;
}

// Better to use prepared statement even for DELETE
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        $_SESSION['success'] = "User deleted successfully.";
    } else {
        $_SESSION['error'] = "User not found or already deleted.";
    }
} else {
    $_SESSION['error'] = "Failed to delete user.";
}

header("Location: manage-users.php");
exit;
?>