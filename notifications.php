<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Mark all as read when page is opened
$conn->query("UPDATE notifications SET is_read = 1 WHERE user_id = $user_id AND is_read = 0");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h2>Notifications</h2>

    <?php
    $result = $conn->query("SELECT * FROM notifications WHERE user_id = $user_id ORDER BY created_at DESC");
    while($row = $result->fetch_assoc()):
    ?>
    <div class="alert alert-<?= $row['type'] ?>">
        <strong><?= htmlspecialchars($row['title']) ?></strong><br>
        <?= htmlspecialchars($row['message']) ?><br>
        <small class="text-muted"><?= date("d M Y H:i", strtotime($row['created_at'])) ?></small>
    </div>
    <?php endwhile; ?>

    <?php if ($result->num_rows == 0): ?>
        <div class="alert alert-info">No notifications yet.</div>
    <?php endif; ?>
</div>

</body>
</html>