<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = (int)($_GET['id'] ?? 0);

if ($id === 0) {
    header("Location: requisitions.php");
    exit;
}

$stmt = $conn->prepare("DELETE FROM requisitions WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: requisitions.php");
exit;
?>