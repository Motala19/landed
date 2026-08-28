<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['finance', 'admin'])) {
    header("Location: login.php");
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id === 0) {
    header("Location: finance-dashboard.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM requisitions WHERE id = ? AND status = 'Paid'");
$stmt->bind_param("i", $id);
$stmt->execute();
$req = $stmt->get_result()->fetch_assoc();

if (!$req) {
    die("Paid requisition not found.");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Replace POP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow-sm p-4" style="max-width:600px;margin:auto;">
        <h4 class="mb-3">
            <?php echo !empty($req['payment_proof']) ? 'Replace Proof of Payment' : 'Upload Proof of Payment'; ?>
        </h4>

        <p class="text-muted">
            Requisition: <strong><?= htmlspecialchars($req['requisition_number']) ?></strong><br>
            Title: <?= htmlspecialchars($req['title']) ?>
        </p>

        <?php if (!empty($req['payment_proof'])): ?>
            <p>
                Current POP:
                <a href="uploads/<?= htmlspecialchars($req['payment_proof']) ?>" target="_blank">
                    View current file
                </a>
            </p>
        <?php endif; ?>

        <form action="save-replace-pop.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $id ?>">

            <div class="mb-3">
                <label class="form-label"><strong>POP File</strong></label>
                <input type="file" name="payment_proof" class="form-control" required>
                <small class="text-muted">PDF, JPG, PNG, DOC, DOCX. Max 5MB</small>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="view-requisition.php?id=<?= $id ?>&type=paid&from=finance" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save POP</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>