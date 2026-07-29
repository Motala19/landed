<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = (int)($_GET['id'] ?? 0);

if ($id === 0) {
    header("Location: finance-dashboard.php");
    exit;
}

// Get requisition info
$result = $conn->query("SELECT * FROM requisitions WHERE id = $id AND status = 'Approved'");
$req = $result->fetch_assoc();

if (!$req) {
    die("Requisition not found or already paid.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pay Requisition</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h3>Pay Requisition #<?php echo $req['requisition_number']; ?></h3>
    <p><strong>Title:</strong> <?php echo htmlspecialchars($req['title']); ?></p>
    <p><strong>Amount:</strong> R<?php echo number_format($req['amount'], 2); ?></p>

    <div class="card mt-4">
        <div class="card-body">
            <form action="process-payment.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $id; ?>">

                <div class="mb-3">
                    <label class="form-label"><strong>Upload Proof of Payment</strong></label>
                    <input type="file" name="payment_proof" class="form-control" required>
                    <small class="text-muted">Upload receipt, bank statement, or proof (PDF, Image)</small>
                </div>

                <button type="submit" class="btn btn-success">Mark as Paid</button>
                <a href="finance-dashboard.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

</body>
</html>