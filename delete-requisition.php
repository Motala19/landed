<?php
include 'includes/db.php';

$id = $_GET['id'];

$conn->query("DELETE FROM requisitions WHERE id = $id");

header("Location: requisitions.php");
exit;
?>