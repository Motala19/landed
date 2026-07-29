<?php
session_start();
include 'includes/db.php';
include 'includes/audit_logger.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = (int)($_GET['id'] ?? 0);

if ($id === 0) {
    header("Location: requisitions.php");
    exit;
}

// Soft delete only
$stmt = $conn->prepare("
    UPDATE requisitions 
    SET deleted_at = NOW(),
        deleted_by = 'staff'
    WHERE id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();

log_audit(
    $_SESSION['user_id'],
    $_SESSION['full_name'] ?? 'Staff',
    $_SESSION['role'],
    'Staff Soft Deleted',
    $id,
    'Removed from staff view'
);

header("Location: requisitions.php");
exit;
?>