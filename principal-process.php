<?php
session_start();
include 'includes/db.php';
include 'includes/audit_logger.php';
include 'includes/notification.php';

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

// Get requisition details
$stmt = $conn->prepare("SELECT budget_status, requisition_number, title, description, amount, created_by 
                        FROM requisitions WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

$budgetStatus = $data['budget_status'] ?? '';
$reqNumber    = $data['requisition_number'] ?? '';
$title        = $data['title'] ?? '';
$description  = $data['description'] ?? '';
$amount       = $data['amount'] ?? 0;
$createdBy    = $data['created_by'] ?? '';

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

    // Notify Treasurer (with details)
    $subject = "Requisition Ready for Treasurer Approval - #$reqNumber";
    $message  = "A requisition has been approved by the Principal and is waiting for your final approval.<br><br>";
    $message .= "<strong>Requisition Number:</strong> $reqNumber<br>";
    $message .= "<strong>Title:</strong> $title<br>";
    $message .= "<strong>Created By:</strong> $createdBy<br>";
    $message .= "<strong>Amount:</strong> R" . number_format($amount, 2) . "<br>";
    $message .= "<strong>Description:</strong><br>" . nl2br(htmlspecialchars($description)) . "<br><br>";
    $message .= "Please log in to the Treasurer Dashboard to action it.";

    send_email_notification("treasurer@midrandprimary.co.za", $subject, $message);
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
}

header("Location: principal-dashboard.php");
exit;
?>