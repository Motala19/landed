<?php
session_start();
include 'includes/db.php';
include 'includes/audit_logger.php';
include 'includes/notification.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id     = (int)($_POST['id'] ?? 0);
$action = $_POST['action'] ?? '';
$reason = $_POST['reason'] ?? '';

if ($id === 0 || !$action) {
    header("Location: treasurer-dashboard.php");
    exit;
}

$user_id   = $_SESSION['user_id'];
$user_name = $_SESSION['full_name'] ?? 'Treasurer User';
$role      = $_SESSION['role'] ?? 'treasurer';

if ($action == 'approve') {

    $stmt = $conn->prepare("UPDATE requisitions 
        SET status = 'Approved',
            action_by = 'Treasurer',
            deleted_by_principal = 0,
            deleted_by_treasurer = 0
        WHERE id = ?");

    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Audit Log
    log_audit($user_id, $user_name, $role, 'Requisition Approved', $id, 'Final Approval by Treasurer');

    // Notify Finance to make payment
    $subject = "Requisition Fully Approved - Please Make Payment";
    $message = "A requisition has been fully approved by the Treasurer.<br><br>";
    $message .= "Please log in to the Finance Dashboard and mark it as Paid after making the payment.";

    send_email_notification("mogalemg@midrandprimary.co.za", $subject, $message);
}

if ($action == 'reject') {

    $stmt = $conn->prepare("UPDATE requisitions 
        SET status = 'Rejected',
            rejection_reason = ?,
            action_by = 'Treasurer'
        WHERE id = ?");

    $stmt->bind_param("si", $reason, $id);
    $stmt->execute();

    // Audit Log
    log_audit($user_id, $user_name, $role, 'Requisition Rejected', $id, "Reason: $reason");
}

header("Location: treasurer-dashboard.php");
exit;
?>