<?php
session_start();
include 'includes/db.php';
include 'includes/audit_logger.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['finance', 'admin'])) {
    header("Location: login.php");
    exit;
}

$id = (int)($_POST['id'] ?? 0);

if ($id === 0 || empty($_FILES['payment_proof']['name'])) {
    header("Location: finance-dashboard.php");
    exit;
}

$check = $conn->prepare("SELECT id FROM requisitions WHERE id = ? AND status = 'Paid'");
$check->bind_param("i", $id);
$check->execute();

if ($check->get_result()->num_rows === 0) {
    die("Paid requisition not found.");
}

if ($_FILES['payment_proof']['size'] > 5 * 1024 * 1024) {
    die("File too large. Max 5MB.");
}

$allowed = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
$ext = strtolower(pathinfo($_FILES['payment_proof']['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $allowed)) {
    die("Invalid file type.");
}

$year = date("Y");
$month = date("m");
$uploadPath = "uploads/payments/$year/$month/";

if (!is_dir($uploadPath)) {
    mkdir($uploadPath, 0755, true);
}

$fileName = time() . "_" . basename($_FILES['payment_proof']['name']);
$fullPath = $uploadPath . $fileName;

if (!move_uploaded_file($_FILES['payment_proof']['tmp_name'], $fullPath)) {
    die("Upload failed.");
}

$proofPath = "payments/$year/$month/" . $fileName;

$stmt = $conn->prepare("UPDATE requisitions SET payment_proof = ? WHERE id = ?");
$stmt->bind_param("si", $proofPath, $id);
$stmt->execute();

log_audit(
    $_SESSION['user_id'],
    $_SESSION['full_name'] ?? 'User',
    $_SESSION['role'],
    'POP Replaced',
    $id,
    "Proof of payment uploaded/replaced"
);

header("Location: view-requisition.php?id=$id&type=paid&from=finance&success=pop_saved");
exit;
?>