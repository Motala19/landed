<?php
session_start();
include 'includes/db.php';
include 'includes/audit_logger.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userName = $_SESSION['full_name'] ?? '';
$userId   = (int)$_SESSION['user_id'];
$role     = $_SESSION['role'] ?? 'staff';
$id       = (int)($_POST['id'] ?? 0);

if ($id === 0 || empty($_FILES['document']['name'])) {
    header("Location: requisitions.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM requisitions WHERE id = ? AND created_by = ? LIMIT 1");
$stmt->bind_param("is", $id, $userName);
$stmt->execute();
$req = $stmt->get_result()->fetch_assoc();

if (!$req) {
    die("Requisition not found or not yours.");
}

if (!empty($req['document'])) {
    header("Location: requisitions.php?error=" . urlencode("Document already uploaded."));
    exit;
}

if ($_FILES['document']['size'] > 5 * 1024 * 1024) {
    header("Location: upload-document.php?id=$id&error=" . urlencode("File too large. Max 5MB."));
    exit;
}

$allowed = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
$ext = strtolower(pathinfo($_FILES['document']['name'], PATHINFO_EXTENSION));
if (!in_array($ext, $allowed)) {
    header("Location: upload-document.php?id=$id&error=" . urlencode("Invalid file type."));
    exit;
}

$year = date("Y");
$month = date("m");
$uploadPath = "uploads/$year/$month/";
if (!is_dir($uploadPath)) {
    mkdir($uploadPath, 0755, true);
}

$fileName = time() . "_" . basename($_FILES['document']['name']);
$fullPath = $uploadPath . $fileName;

if (!move_uploaded_file($_FILES['document']['tmp_name'], $fullPath)) {
    header("Location: upload-document.php?id=$id&error=" . urlencode("Upload failed."));
    exit;
}

$documentPath = "$year/$month/" . $fileName;

$upd = $conn->prepare("UPDATE requisitions SET document = ? WHERE id = ? AND created_by = ?");
$upd->bind_param("sis", $documentPath, $id, $userName);
$upd->execute();

log_audit($userId, $userName, $role, 'Document Uploaded', $id, "Late document upload for " . $req['requisition_number']);

header("Location: requisitions.php?success=document_uploaded");
exit;
?>