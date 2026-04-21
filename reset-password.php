<?php
session_start();
include 'includes/db.php';

// Security: Only Finance and Admin can reset passwords
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['finance', 'admin'])) {
    header("Location: login.php");
    exit;
}

// Validate ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid user ID.";
    header("Location: manage-users.php");
    exit;
}

$id = (int)$_GET['id'];

// Prevent resetting your own password this way (optional but recommended)
if ($id == $_SESSION['user_id']) {
    $_SESSION['error'] = "You cannot reset your own password here.";
    header("Location: manage-users.php");
    exit;
}

// Generate new temporary password
$temp_password = "Temp" . rand(1000, 9999);
$hashed = password_hash($temp_password, PASSWORD_DEFAULT);

// Update user with new password and force change on next login
$stmt = $conn->prepare("UPDATE users 
                        SET password = ?, 
                            must_change_password = 1 
                        WHERE id = ?");

$stmt->bind_param("si", $hashed, $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        $_SESSION['success'] = "Password reset successfully for the user!<br>
                                <strong>New Temporary Password:</strong> <code>$temp_password</code><br>
                                Please give this password to the user.";
    } else {
        $_SESSION['error'] = "User not found.";
    }
} else {
    $_SESSION['error'] = "Failed to reset password.";
}

header("Location: manage-users.php");
exit;
?>