<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id           = (int)($_POST['id'] ?? 0);
$action       = $_POST['action'] ?? 'update';
$title        = trim($_POST['title'] ?? '');
$department   = trim($_POST['department'] ?? '');
$amount       = (float)($_POST['amount'] ?? 0);
$payment_type = trim($_POST['payment_type'] ?? '');
$payable_to   = trim($_POST['payable_to'] ?? '');
$description  = trim($_POST['description'] ?? '');

if ($id === 0) {
    header("Location: requisitions.php");
    exit;
}

// Handle file upload safely
$documentName = null;

if (!empty($_FILES['document']['name'])) {
    // Basic size check
    if ($_FILES['document']['size'] > 5 * 1024 * 1024) {
        die("File too large. Max 5MB.");
    }

    $year = date("Y");
    $month = date("m");
    $uploadPath = "uploads/$year/$month/";

    if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0755, true);
    }

    $fileName = time() . "_" . basename($_FILES['document']['name']);
    $fullPath = $uploadPath . $fileName;

    if (move_uploaded_file($_FILES['document']['tmp_name'], $fullPath)) {
        $documentName = "$year/$month/$fileName";
    }
}

// Update with prepared statement
if ($documentName) {
    $stmt = $conn->prepare("
        UPDATE requisitions SET
            title = ?,
            department = ?,
            amount = ?,
            payment_type = ?,
            payable_to = ?,
            description = ?,
            document = ?
        WHERE id = ?
    ");
    $stmt->bind_param("ssdssssi", $title, $department, $amount, $payment_type, $payable_to, $description, $documentName, $id);
} else {
    $stmt = $conn->prepare("
        UPDATE requisitions SET
            title = ?,
            department = ?,
            amount = ?,
            payment_type = ?,
            payable_to = ?,
            description = ?
        WHERE id = ?
    ");
    $stmt->bind_param("ssdsssi", $title, $department, $amount, $payment_type, $payable_to, $description, $id);
}

$stmt->execute();

// Resubmit logic
if ($action === 'resubmit') {
    $stmt2 = $conn->prepare("
        UPDATE requisitions SET
            status = 'New',
            rejection_reason = NULL,
            action_by = 'Staff'
        WHERE id = ?
    ");
    $stmt2->bind_param("i", $id);
    $stmt2->execute();
}

header("Location: requisitions.php");
exit;
?>