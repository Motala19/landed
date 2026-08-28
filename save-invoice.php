<?php
session_start();
include 'includes/db.php';
include 'includes/audit_logger.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = (int)($_POST['id'] ?? 0);
$userName = $_SESSION['full_name'] ?? 'User';
$role = $_SESSION['role'] ?? 'staff';

if ($id === 0 || empty($_FILES['invoice']['name'])) {
    header("Location: requisitions.php?error=Please select an invoice file");
    exit;
}

// Check requisition is Paid
$stmt = $conn->prepare("SELECT * FROM requisitions WHERE id = ? AND status = 'Paid'");
$stmt->bind_param("i", $id);
$stmt->execute();
$req = $stmt->get_result()->fetch_assoc();

if (!$req) {
    die("Requisition not found or not paid.");
}

if ($role === 'staff' && $req['created_by'] !== $userName) {
    die("Not allowed.");
}

// File validation
if ($_FILES['invoice']['size'] > 5 * 1024 * 1024) {
    header("Location: upload-invoice.php?id=$id&error=File too large. Max 5MB");
    exit;
}

$allowed = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
$ext = strtolower(pathinfo($_FILES['invoice']['name'], PATHINFO_EXTENSION));
if (!in_array($ext, $allowed)) {
    header("Location: upload-invoice.php?id=$id&error=Invalid file type");
    exit;
}

// Save file
$year = date("Y");
$month = date("m");
$uploadPath = "uploads/invoices/$year/$month/";

if (!is_dir($uploadPath)) {
    mkdir($uploadPath, 0755, true);
}

$fileName = time() . "_" . basename($_FILES['invoice']['name']);
$fullPath = $uploadPath . $fileName;

if (!move_uploaded_file($_FILES['invoice']['tmp_name'], $fullPath)) {
    header("Location: upload-invoice.php?id=$id&error=Upload failed");
    exit;
}

$invoicePath = "invoices/$year/$month/" . $fileName;

// Update database
$update = $conn->prepare("UPDATE requisitions SET invoice = ? WHERE id = ?");
$update->bind_param("si", $invoicePath, $id);
$update->execute();

log_audit($_SESSION['user_id'], $userName, $role, 'Invoice Uploaded', $id, "Invoice file uploaded");

header("Location: requisitions.php?success=invoice_uploaded");
exit;
?>