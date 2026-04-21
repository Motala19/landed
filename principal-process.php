<?php
include 'includes/db.php';

$id = (int)($_POST['id'] ?? 0);
$action = $_POST['action'] ?? '';

$approveReason = $_POST['approve_reason'] ?? '';
$rejectReason = $_POST['reject_reason'] ?? '';

if ($id === 0) {
    header("Location: principal-dashboard.php");
    exit;
}

// GET CURRENT DATA
$result = $conn->query("SELECT budget_status FROM requisitions WHERE id = $id");
$data = $result->fetch_assoc();
$budgetStatus = $data['budget_status'] ?? '';

// =============================
// APPROVE
// =============================
if ($action == 'approve') {

    if ($budgetStatus == 'No' && empty($approveReason)) {
        die("Principal must provide reason when approving outside budget.");
    }

    $stmt = $conn->prepare("UPDATE requisitions 
        SET status = 'Principal Approved',
            principal_reason = ?,
            action_by = 'Principal',
            deleted_by_treasurer = 0
        WHERE id = ?");

    $stmt->bind_param("si", $approveReason, $id);
    $stmt->execute();
}

// =============================
// REJECT
// =============================
if ($action == 'reject') {

    $stmt = $conn->prepare("UPDATE requisitions 
        SET status = 'Rejected',
            rejection_reason = ?,
            action_by = 'Principal'
        WHERE id = ?");

    $stmt->bind_param("si", $rejectReason, $id);
    $stmt->execute();
}

header("Location: principal-dashboard.php");
exit;
?>