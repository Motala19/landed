<?php
session_start();
include 'includes/db.php';
include 'includes/audit_logger.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = (int)($_POST['id'] ?? 0);

if ($id === 0) {
    header("Location: finance-dashboard.php");
    exit;
}

// Check if requisition is Approved
$check = $conn->query("SELECT status FROM requisitions WHERE id = $id");
if ($check->fetch_assoc()['status'] !== 'Approved') {
    die("Only approved requisitions can be marked as paid.");
}

// File Upload for Proof of Payment - Store full relative path
$proofToStore = '';

if (!empty($_FILES['payment_proof']['name'])) {
    if ($_FILES['payment_proof']['size'] > 5 * 1024 * 1024) {
        die("Proof of payment file is too large. Max 5MB.");
    }

    $year = date("Y");
    $month = date("m");
    $uploadPath = "uploads/payments/$year/$month/";

    if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0755, true);
    }

    $proofName = time() . "_" . basename($_FILES['payment_proof']['name']);
    $fullUploadPath = $uploadPath . $proofName;

    if (move_uploaded_file($_FILES['payment_proof']['tmp_name'], $fullUploadPath)) {
        $proofToStore = "payments/$year/$month/" . $proofName;
    }
}

// Update to Paid
$stmt = $conn->prepare("UPDATE requisitions 
    SET status = 'Paid', 
        payment_proof = ?,
        paid_at = NOW(),
        paid_by = ?,
        deleted_by_principal = 0,
        deleted_by_treasurer = 0
    WHERE id = ?");

$paid_by = $_SESSION['full_name'] ?? 'System';
$stmt->bind_param("ssi", $proofToStore, $paid_by, $id);
$stmt->execute();

log_audit($_SESSION['user_id'], $_SESSION['full_name'] ?? 'User', $_SESSION['role'], 'Payment Made', $id, "Proof uploaded");

header("Location: finance-dashboard.php?success=paid");
exit;
?>