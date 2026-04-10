<?php
session_start();

// 🚫 Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

include 'includes/db.php';

$userName = "Principal User";

$id = $_GET['id'] ?? 0;

$result = $conn->query("SELECT * FROM requisitions WHERE id = $id");
$requisition = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Principal Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="principal-dashboard.php" class="btn btn-secondary mb-2">← Back</a>
        <h3>Principal Verification</h3>
    </div>

    <div class="text-end">
        <strong><?php echo $userName; ?></strong><br>
        <small class="text-muted">Principal</small>
    </div>
</div>

<!-- FORM -->
<div class="card shadow-sm p-4">

<form method="POST" action="principal-process.php">

<input type="hidden" name="id" value="<?php echo $requisition['id']; ?>">

<div class="row">

<!-- STAFF -->
<div class="col-md-6 mb-3">
<label><strong>Submitted By</strong></label>
<input class="form-control bg-success text-white"
value="<?php echo $requisition['created_by']; ?>" readonly>
</div>

<!-- TITLE -->
<div class="col-md-6 mb-3">
<label><strong>Requisition Title</strong></label>
<input class="form-control bg-success text-white"
value="<?php echo $requisition['title']; ?>" readonly>
</div>

<!-- DEPARTMENT -->
<div class="col-md-6 mb-3">
<label><strong>Department</strong></label>
<input class="form-control bg-success text-white"
value="<?php echo $requisition['department']; ?>" readonly>
</div>

<!-- AMOUNT -->
<div class="col-md-6 mb-3">
<label><strong>Amount (R)</strong></label>
<input class="form-control bg-success text-white"
value="<?php echo $requisition['amount']; ?>" readonly>
</div>

<!-- PAYMENT -->
<div class="col-md-6 mb-3">
<label><strong>Payment Type</strong></label>
<input class="form-control bg-success text-white"
value="<?php echo $requisition['payment_type']; ?>" readonly>
</div>

<!-- PAYABLE -->
<div class="col-md-6 mb-3">
<label><strong>To Whom Payable</strong></label>
<input class="form-control bg-success text-white"
value="<?php echo $requisition['payable_to']; ?>" readonly>
</div>

<!-- DESCRIPTION -->
<div class="col-12 mb-3">
<label><strong>Description</strong></label>
<textarea class="form-control bg-success text-white" rows="5" readonly><?php echo $requisition['description']; ?></textarea>
</div>

<!-- ✅ BUDGET STATUS (READ ONLY FROM FINANCE) -->
<div class="col-md-6 mb-3">
<label><strong>Expense Within Approved Budget?</strong></label>
<input class="form-control bg-info text-white"
value="<?php echo $requisition['budget_status'] ?? 'Not Set'; ?>" readonly>
</div>

<!-- DOCUMENT -->
<div class="col-12 mb-3">
<label><strong>Supporting Document</strong></label>
<div>
<?php if(!empty($requisition['document'])): ?>
<a href="uploads/<?php echo $requisition['document']; ?>" target="_blank" class="btn btn-outline-primary">
View Document
</a>
<?php else: ?>
<span class="text-muted">No document</span>
<?php endif; ?>
</div>
</div>

<!-- ✅ APPROVAL REASON -->
<div class="col-12 mb-3">
<label><strong>Reason for Approval (Required if NOT within budget)</strong></label>
<textarea name="approve_reason" class="form-control"
placeholder="Explain why approving outside budget..."></textarea>
</div>

<!-- ✅ REJECTION REASON -->
<div class="col-12 mb-3">
<label><strong>Reason for Rejection</strong></label>
<textarea name="reject_reason" class="form-control"
placeholder="Explain why rejecting this requisition..."></textarea>
</div>

</div>

<!-- INFO -->
<div class="alert alert-warning mt-3">
Principal must provide a reason when approving outside budget or rejecting.
</div>

<!-- BUTTONS -->
<div class="d-flex justify-content-end mt-3">
<button type="submit" name="action" value="reject" class="btn btn-danger me-2">Reject</button>
<button type="submit" name="action" value="approve" class="btn btn-success">Approve</button>
</div>

</form>

</div>

</div>

</body>
</html>