<?php
session_start();
include 'includes/db.php';
include 'includes/audit_logger.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['finance', 'admin'])) {
    header("Location: login.php");
    exit;
}

$id = (int)($_GET['id'] ?? 0);

if ($id === 0) {
    header("Location: finance-dashboard.php");
    exit;
}

// Use prepared statement
$stmt = $conn->prepare("DELETE FROM requisitions WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

// Optional: Log the action
log_audit(
    $_SESSION['user_id'],
    $_SESSION['full_name'] ?? 'Finance',
    $_SESSION['role'],
    'Hard Deleted Requisition',
    $id,
    'Permanently deleted by Finance'
);

header("Location: finance-dashboard.php");
exit;
?>