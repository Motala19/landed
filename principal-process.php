<?php
include 'includes/db.php';

$id = $_POST['id'] ?? 0;
$action = $_POST['action'] ?? '';
$approveReason = $_POST['approve_reason'] ?? '';
$rejectReason = $_POST['reject_reason'] ?? '';

if ($action == 'approve') {

    $conn->query("UPDATE requisitions 
SET status='Approved',
    action_by='Principal'
WHERE id=$id");

}

if ($action == 'reject') {

    $conn->query("UPDATE requisitions 
SET status='Rejected',
    rejection_reason='$reason',
    action_by='Principal'
WHERE id=$id");

}

header("Location: principal-dashboard.php");
exit;
?>