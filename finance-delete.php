<?php
include 'includes/db.php';

$id = $_GET['id'];

// HARD DELETE (ONLY FINANCE)
$conn->query("DELETE FROM requisitions WHERE id=$id");

header("Location: finance-dashboard.php");
exit;
?>