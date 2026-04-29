<?php
/* include 'includes/db.php';

$id = (int)($_GET['id'] ?? 0);
$role = strtolower(trim($_GET['role'] ?? ''));

if ($id === 0 || !in_array($role, ['principal', 'treasurer', 'finance'])) {
    header("Location: requisitions.php");
    exit;
}

$column = "deleted_by_" . $role;

$stmt = $conn->prepare("UPDATE requisitions SET `$column` = 1 WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$redirect = $role . "-dashboard.php";
header("Location: $redirect");
exit;*/
?>