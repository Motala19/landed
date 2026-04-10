<?php
session_start();
include 'includes/db.php';



// ✅ SAFE FETCH
$id = $_POST['id'] ?? null;
$action = $_POST['action'] ?? null;
$budget = $_POST['budget_check'] ?? '';
$reason = $_POST['reason'] ?? '';

// 🚨 STOP if not coming from form
if (!$id || !$action) {
    header("Location: finance-dashboard.php");
    exit;
}

if ($action == 'verify') {

    // ✅ Approve finance step

    $conn->query("UPDATE requisitions 
SET status='Pending Principal', 
    budget_status='$budget',
    action_by='Finance'
WHERE id=$id");

    header("Location: finance-dashboard.php");
    exit;
}




if ($action == 'reject') {

    // ❌ Reject and send back to staff
    $conn->query("UPDATE requisitions 
    SET status='Rejected', 
    rejection_reason='$reason', 
    budget_status='$budget',
    action_by='Finance'
    WHERE id=$id");

    header("Location: finance-dashboard.php");
    exit;
    
}
?>
