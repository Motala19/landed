<?php
session_start();
include 'includes/db.php';
include 'includes/audit_logger.php';
include 'includes/notification.php';   // ← Added for email

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id            = (int)($_POST['id'] ?? 0);
$action        = $_POST['action'] ?? '';
$approveReason = $_POST['approve_reason'] ?? '';
$rejectReason  = $_POST['reject_reason'] ?? '';

if ($id === 0) {
    header("Location: principal-dashboard.php");
    exit;
}

$user_id   = $_SESSION['user_id'];
$user_name = $_SESSION['full_name'] ?? 'Principal User';
$role      = $_SESSION['role'] ?? 'principal';

// Get current budget status
$result = $conn->query("SELECT budget_status FROM requisitions WHERE id = $id");
$data = $result->fetch_assoc();
$budgetStatus = $data['budget_status'] ?? '';

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

    // Audit Log
    log_audit($user_id, $user_name, $role, 'Requisition Approved', $id, "Principal Reason: $approveReason");

    // Notify Treasurer
    send_email_notification("treasurer@midrandprimary.co.za", 
        "Requisition Approved by Principal", 
        "A requisition has been approved by Principal and is now waiting for your final approval.");

} 

if ($action == 'reject') {

    $stmt = $conn->prepare("UPDATE requisitions 
        SET status = 'Rejected',
            rejection_reason = ?,
            action_by = 'Principal'
        WHERE id = ?");

    $stmt->bind_param("si", $rejectReason, $id);
    $stmt->execute();

    // Audit Log
    log_audit($user_id, $user_name, $role, 'Requisition Rejected', $id, "Reason: $rejectReason");

    // Notify Staff / Creator
    send_email_notification("staff@midrandprimary.co.za", 
        "Requisition Rejected by Principal", 
        "Your requisition was rejected by Principal. Reason: $rejectReason");
}

header("Location: principal-dashboard.php");
exit;
?>