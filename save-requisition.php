<?php
session_start();
include 'includes/db.php';
include 'includes/audit_logger.php';        // ← Added this line

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

// FILE UPLOAD
$documentName = '';

if (!empty($_FILES['document']['name'])) {
    $documentName = time() . "_" . basename($_FILES['document']['name']);
    move_uploaded_file($_FILES['document']['tmp_name'], "uploads/" . $documentName);
}

// 🔥 SAFE NUMBER GENERATION + AUDIT LOG
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

    // INSERT REQUISITION
    $sql = "INSERT INTO requisitions 
            (requisition_number, title, department, amount, payment_type, payable_to, description, document, created_by, status) 
            VALUES 
            ('$reqNumber', '$title', '$department', '$amount', '$payment_type', '$payable_to', '$description', '$documentName', '$user_name', 'New')";

    if (!$conn->query($sql)) {
        throw new Exception("Failed to insert requisition");
    }

    $new_requisition_id = $conn->insert_id;

    $conn->commit();

    // ==================== AUDIT LOG ====================
    log_audit(
        $user_id,
        $user_name,
        $role,
        'Requisition Created',
        $new_requisition_id,
        "Requisition Number: $reqNumber | Title: $title | Amount: R" . number_format($amount, 2)
    );

    // Redirect with success
    header("Location: requisitions.php?success=created");
    exit;

} catch (Exception $e) {
    $conn->rollback();
    $conn->query("UNLOCK TABLES");
    die("Error: " . $e->getMessage());
}
?>