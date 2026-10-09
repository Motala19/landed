<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userName = $_SESSION['full_name'] ?? '';
$id = (int)($_GET['id'] ?? 0);

if ($id === 0) {
    header("Location: requisitions.php");
    exit;
}

// Only owner can upload, and only if no document yet
$stmt = $conn->prepare("SELECT * FROM requisitions WHERE id = ? AND created_by = ? LIMIT 1");
$stmt->bind_param("is", $id, $userName);
$stmt->execute();
$req = $stmt->get_result()->fetch_assoc();

if (!$req) {
    die("Requisition not found or not yours.");
}

if (!empty($req['document'])) {
    die("This requisition already has a document.");
}

$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Upload Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5" style="max-width:600px;">
    <div class="card shadow-sm p-4">
        <h4 class="mb-3">Upload Supporting Document</h4>
        <p class="text-muted">
            Requisition: <strong><?= htmlspecialchars($req['requisition_number']) ?></strong><br>
            Title: <?= htmlspecialchars($req['title']) ?>
        </p>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="save-document.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $id ?>">
            <div class="mb-3">
                <label class="form-label"><strong>Document</strong></label>
                <input type="file" name="document" class="form-control" required>
                <small class="text-muted">PDF, JPG, PNG, DOC, DOCX. Max 5MB</small>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <a href="requisitions.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Upload</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>