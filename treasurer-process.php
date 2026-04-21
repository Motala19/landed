<?php
session_start();
include 'includes/db.php';

$id = (int)($_POST['id'] ?? 0);
$action = $_POST['action'] ?? '';
$reason = $_POST['reason'] ?? '';

if ($id === 0 || !$action) {
    header("Location: treasurer-dashboard.php");
    exit;
}

if ($action == 'approve') {

    $stmt = $conn->prepare("UPDATE requisitions 
        SET status = 'Approved',
            action_by = 'Treasurer',
            deleted_by_principal = 0,
            deleted_by_treasurer = 0
        WHERE id = ?");

    $stmt->bind_param("i", $id);
    $stmt->execute();
}

if ($action == 'reject') {

    $stmt = $conn->prepare("UPDATE requisitions 
        SET status = 'Rejected',
            rejection_reason = ?,
            action_by = 'Treasurer'
        WHERE id = ?");

    $stmt->bind_param("si", $reason, $id);
    $stmt->execute();
}

header("Location: treasurer-dashboard.php");
exit;
?>