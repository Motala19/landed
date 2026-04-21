<?php
include 'includes/db.php';

$id           = $_POST['id'];
$action       = $_POST['action'] ?? 'update'; // 🔥 NEW

$title        = $_POST['title'];
$department   = $_POST['department'];
$amount       = $_POST['amount'];
$payment_type = $_POST['payment_type'];
$payable_to   = $_POST['payable_to'];
$description  = $_POST['description'];

// HANDLE FILE UPDATE
if (!empty($_FILES['document']['name'])) {
    $documentName = time() . "_" . $_FILES['document']['name'];
    move_uploaded_file($_FILES['document']['tmp_name'], "uploads/" . $documentName);

    $sql = "UPDATE requisitions SET
        title='$title',
        department='$department',
        amount='$amount',
        payment_type='$payment_type',
        payable_to='$payable_to',
        description='$description',
        document='$documentName'
        WHERE id=$id";
} else {
    $sql = "UPDATE requisitions SET
        title='$title',
        department='$department',
        amount='$amount',
        payment_type='$payment_type',
        payable_to='$payable_to',
        description='$description'
        WHERE id=$id";
}

$conn->query($sql);


// 🔥 RESUBMIT LOGIC (THIS IS THE MAGIC)
if ($action == 'resubmit') {

    $conn->query("UPDATE requisitions SET
        status='new',
        rejection_reason=NULL,
        action_by='Staff'
    WHERE id=$id");

}


header("Location: requisitions.php");
exit;
?>