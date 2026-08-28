<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = (int)($_GET['id'] ?? 0);
$userName = $_SESSION['full_name'] ?? 'User';

if ($id === 0) {
    header("Location: requisitions.php");
    exit;
}

// Only allow Paid requisitions that belong to this user (or admin/finance)
$stmt = $conn->prepare("SELECT * FROM requisitions WHERE id = ? AND status = 'Paid'");
$stmt->bind_param("i", $id);
$stmt->execute();
$req = $stmt->get_result()->fetch_assoc();

if (!$req) {
    die("Requisition not found or not paid yet.");
}

// Staff can only upload for their own requisitions
if ($_SESSION['role'] === 'staff' && $req['created_by'] !== $userName) {
    die("You can only upload invoices for your own requisitions.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Invoice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow-sm p-4" style="max-width: 600px; margin: auto;">
        <h4 class="mb-3">Upload Invoice</h4>
        <p class="text-muted mb-4">
            Requisition: <strong><?= htmlspecialchars($req['requisition_number']) ?></strong><br>
            Title: <?= htmlspecialchars($req['title']) ?>
        </p>

        <form action="save-invoice.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $id ?>">

            <div class="mb-3">
                <label class="form-label"><strong>Invoice File</strong></label>
                <input type="file" name="invoice" class="form-control" required>
                <small class="text-muted">PDF, JPG, PNG, DOC, DOCX. Max 5MB</small>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="requisitions.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Upload Invoice</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>