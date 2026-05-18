<?php
session_start();
include 'includes/db.php';
include 'includes/audit_logger.php';   // ← Important

$id   = (int)($_GET['id'] ?? 0);
$role = strtolower(trim($_GET['role'] ?? ''));

if ($id === 0 || !in_array($role, ['principal', 'treasurer', 'finance', 'staff'])) {
    header("Location: requisitions.php");
    exit;
}

$user_id   = $_SESSION['user_id'] ?? 0;
$user_name = $_SESSION['full_name'] ?? 'Unknown User';
$current_role = $_SESSION['role'] ?? $role;

$column = "deleted_by_" . $role;

// Perform soft delete
$stmt = $conn->prepare("UPDATE requisitions SET `$column` = 1 WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

// Audit Log
log_audit(
    $user_id,
    $user_name,
    $current_role,
    'Requisition Soft Deleted',
    $id,
    "Removed from $role view"
);

// Redirect to correct dashboard
if ($role === 'staff') {
    header("Location: requisitions.php");
} else {
    header("Location: " . $role . "-dashboard.php");
}
exit;
?>