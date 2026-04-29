<?php

include 'includes/db.php';

$id = $_GET['id'];

// SOFT DELETE ONLY (DO NOT REMOVE DATA)
$conn->query("
    UPDATE requisitions 
    SET deleted_at = NOW(),
        deleted_by = 'staff'
    WHERE id = $id
");

header("Location: requisitions.php");
exit; 
?>