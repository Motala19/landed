<?php
session_start();
include 'includes/db.php';
include 'includes/audit_logger.php';
include 'includes/notification.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id   = $_SESSION['user_id'];
$user_name = $_SESSION['full_name'] ?? 'Unknown User';
$role      = $_SESSION['role'] ?? 'staff';

// FORM DATA
$title        = trim($_POST['title'] ?? '');
$department   = trim($_POST['department'] ?? '');
$amount       = (float)($_POST['amount'] ?? 0);
$payment_type = trim($_POST['payment_type'] ?? '');
$payable_to   = trim($_POST['payable_to'] ?? '');
$description  = trim($_POST['description'] ?? '');

// Basic validation
if (empty($title) || empty($department) || $amount <= 0 || empty($payment_type) || empty($payable_to) || empty($description)) {
    header("Location: create_requisition.php?error=Please fill in all required fields correctly.");
    exit;
}

// FILE UPLOAD - Organized by Year/Month + 5MB limit + Store full relative path
$documentName = '';

if (!empty($_FILES['document']['name'])) {
    if ($_FILES['document']['size'] > 5 * 1024 * 1024) {
        header("Location: create_requisition.php?error=File is too large. Maximum size is 5MB.");
        exit;
    }

    // Optional: Basic file type check
    $allowed = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
    $ext = strtolower(pathinfo($_FILES['document']['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) {
        header("Location: create_requisition.php?error=Invalid file type. Allowed: PDF, JPG, PNG, DOC, DOCX.");
        exit;
    }

    $year = date("Y");
    $month = date("m");
    $uploadPath = "uploads/$year/$month/";

    if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0755, true);
    }

    $fileName = time() . "_" . basename($_FILES['document']['name']);
    $fullUploadPath = $uploadPath . $fileName;

    if (move_uploaded_file($_FILES['document']['tmp_name'], $fullUploadPath)) {
        $documentName = "$year/$month/" . $fileName;
    }
}

// SAFE NUMBER GENERATION + INSERT
$conn->begin_transaction();

try {
    $year = date("Y");

    // Lock table
    $conn->query("LOCK TABLES requisition_counters WRITE");

    $result = $conn->query("SELECT last_number FROM requisition_counters WHERE year = $year");

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $newNumber = $row['last_number'] + 1;
        $conn->query("UPDATE requisition_counters SET last_number = $newNumber WHERE year = $year");
    } else {
        $newNumber = 1;
        $conn->query("INSERT INTO requisition_counters (year, last_number) VALUES ($year, 1)");
    }

    $conn->query("UNLOCK TABLES");

    $reqNumber = "REQ-$year-" . str_pad($newNumber, 4, "0", STR_PAD_LEFT);

    // PREPARED STATEMENT INSERT
    $stmt = $conn->prepare("
        INSERT INTO requisitions 
        (requisition_number, title, department, amount, payment_type, payable_to, description, document, created_by, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'New')
    ");

    $stmt->bind_param(
        "sssdsssss",
        $reqNumber,
        $title,
        $department,
        $amount,
        $payment_type,
        $payable_to,
        $description,
        $documentName,
        $user_name
    );

    if (!$stmt->execute()) {
        throw new Exception("Failed to insert requisition");
    }

    $new_requisition_id = $conn->insert_id;

    $conn->commit();

    // Audit Log
    log_audit(
        $user_id,
        $user_name,
        $role,
        'Requisition Created',
        $new_requisition_id,
        "Requisition Number: $reqNumber | Title: $title | Amount: R" . number_format($amount, 2)
    );

    // Notify Finance
    $subject = "New Requisition Submitted - #$reqNumber";
    $message = "A new requisition has been submitted by $user_name.<br><br>";
    $message .= "Title: $title<br>";
    $message .= "Amount: R" . number_format($amount, 2) . "<br>";
    $message .= "Please review it in the Finance Dashboard.";

    send_email_notification("mogalemg@midrandprimary.co.za", $subject, $message);

    header("Location: requisitions.php?success=created");
    exit;

} catch (Exception $e) {
    $conn->rollback();
    $conn->query("UNLOCK TABLES");
    header("Location: create_requisition.php?error=" . urlencode($e->getMessage()));
    exit;
}
?>