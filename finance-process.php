<?php
session_start();
include 'includes/db.php';
include 'includes/audit_logger.php';   // ← Added

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id     = (int)($_POST['id'] ?? 0);
$action = $_POST['action'] ?? null;
$budget = $_POST['budget_check'] ?? '';
$reason = $_POST['reason'] ?? '';

if ($id === 0 || !$action) {
    header("Location: finance-dashboard.php");
    exit;
}

$user_id   = $_SESSION['user_id'];
$user_name = $_SESSION['full_name'] ?? 'Finance User';
$role      = $_SESSION['role'] ?? 'finance';

if ($action == 'verify') {

    $stmt = $conn->prepare("UPDATE requisitions 
        SET status = 'Pending', 
            budget_status = ?,
            action_by = 'Finance',
            deleted_by_principal = 0,
            deleted_by_treasurer = 0 
        WHERE id = ?");

    $stmt->bind_param("si", $budget, $id);
    $stmt->execute();

    // Audit Log
    log_audit($user_id, $user_name, $role, 'Requisition Verified', $id, "Budget Status: $budget");

    header("Location: finance-dashboard.php?success=verified");
    exit;
}

if ($action == 'reject') {

    $stmt = $conn->prepare("UPDATE requisitions 
        SET status = 'Rejected', 
            rejection_reason = ?,
            budget_status = ?,
            action_by = 'Finance'
        WHERE id = ?");

    $stmt->bind_param("ssi", $reason, $budget, $id);
    $stmt->execute();

    // Audit Log
    log_audit($user_id, $user_name, $role, 'Requisition Rejected', $id, "Reason: $reason");

    header("Location: finance-dashboard.php?success=rejected");
    exit;
}
?>