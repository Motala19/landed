<?php
session_start();
include 'includes/db.php';

$id = $_POST['id'];
$action = $_POST['action'];
$reason = $_POST['reason'] ?? '';

if ($action == 'verify') {

    // ✅ Move to Principal
    $conn->query("UPDATE requisitions 
                  SET status='Pending Principal' 
                  WHERE id=$id");

    header("Location: finance-requisitions.php");
    exit;

}

if ($action == 'reject') {

    // ❌ Reject and send back to staff
    $conn->query("UPDATE requisitions 
                  SET status='Rejected', rejection_reason='$reason' 
                  WHERE id=$id");

    header("Location: requisitions.php");
    exit;
}
?>