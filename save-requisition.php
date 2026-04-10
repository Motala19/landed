<?php
session_start();
include 'includes/db.php';

$userName = $_SESSION['userName'] ?? 'Motala Godfrey';

// FORM DATA
$title        = $_POST['title'];
$department   = $_POST['department'];
$amount       = $_POST['amount'];
$payment_type = $_POST['payment_type'];
$payable_to   = $_POST['payable_to'];
$description  = $_POST['description'];

// FILE UPLOAD
$documentName = '';

if (!empty($_FILES['document']['name'])) {
    $documentName = time() . "_" . $_FILES['document']['name'];
    move_uploaded_file($_FILES['document']['tmp_name'], "uploads/" . $documentName);
}


// 🔥 SAFE NUMBER GENERATION (FINAL)
$conn->begin_transaction();

try {
    $year = date("Y");

    // 🔒 LOCK TABLE (STRONG LOCK)
    $conn->query("LOCK TABLES requisition_counters WRITE");

    // GET CURRENT VALUE
    $result = $conn->query("SELECT last_number FROM requisition_counters WHERE year = $year");

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $newNumber = $row['last_number'] + 1;

        $conn->query("UPDATE requisition_counters SET last_number = $newNumber WHERE year = $year");
    } else {
        $newNumber = 1;
        $conn->query("INSERT INTO requisition_counters (year, last_number) VALUES ($year, 1)");
    }

    // UNLOCK TABLE
    $conn->query("UNLOCK TABLES");

    // FORMAT NUMBER
    $reqNumber = "REQ-$year-" . str_pad($newNumber, 4, "0", STR_PAD_LEFT);

    // INSERT
    $sql = "INSERT INTO requisitions 
(requisition_number, title, department, amount, payment_type, payable_to, description, document, created_by, status)
VALUES 
('$reqNumber', '$title', '$department', '$amount', '$payment_type', '$payable_to', '$description', '$documentName', '$userName', 'Pending')";

    $conn->query($sql);

    $conn->commit();

} catch (Exception $e) {
    $conn->rollback();
    $conn->query("UNLOCK TABLES");
    die("Error: " . $e->getMessage());
}

// REDIRECT
header("Location: requisitions.php");
exit;
?>