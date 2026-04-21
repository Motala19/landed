<?php
session_start();
include 'includes/db.php';

// ✅ SAFE FETCH
$id = (int)($_POST['id'] ?? 0);
$action = $_POST['action'] ?? null;
$budget = $_POST['budget_check'] ?? '';
$reason = $_POST['reason'] ?? '';

// 🚨 STOP if not coming from form
if ($id === 0 || !$action) {
    header("Location: finance-dashboard.php");
    exit;
}

if ($action == 'verify') {

    // ✅ Verify and send to Principal - reset deleted flags
    $stmt = $conn->prepare("UPDATE requisitions 
        SET status = 'Pending', 
            budget_status = ?,
            action_by = 'Finance',
            deleted_by_principal = 0,
            deleted_by_treasurer = 0 
        WHERE id = ?");

    $stmt->bind_param("si", $budget, $id);
    $stmt->execute();

    header("Location: finance-dashboard.php");
    exit;
}

if ($action == 'reject') {

    // ❌ Reject
    $stmt = $conn->prepare("UPDATE requisitions 
        SET status = 'Rejected', 
            rejection_reason = ?,
            budget_status = ?,
            action_by = 'Finance'
        WHERE id = ?");

    $stmt->bind_param("ssi", $reason, $budget, $id);
    $stmt->execute();

    header("Location: finance-dashboard.php");
    exit;
}
?>