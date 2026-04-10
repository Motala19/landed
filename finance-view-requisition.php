<?php 
session_start();
$userName = "Finance User";

include 'includes/db.php';

$id = $_GET['id'] ?? 0;

$result = $conn->query("SELECT * FROM requisitions WHERE id = $id");
$requisition = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Requisition</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="finance-dashboard.php" class="btn btn-secondary mb-2">← Back</a>
            <h3>View Requisition</h3>
        </div>

        <div class="text-end">
            <strong><?php echo $userName; ?></strong><br>
            <small class="text-muted">Finance</small>
        </div>
    </div>

    <!-- FORM (READ ONLY) -->
    <div class="card shadow-sm p-4">

        <div class="row">

            <!-- STAFF MEMBER -->
            <div class="col-md-6 mb-3">
                <label class="form-label"><strong>Submitted By</strong></label>
                <input type="text" class="form-control bg-success text-white"
                       value="<?php echo $requisition['created_by'] ?? ''; ?>" readonly>
            </div>

            <!-- TITLE -->
            <div class="col-md-6 mb-3">
                <label class="form-label"><strong>Requisition Title</strong></label>
                <input type="text" class="form-control bg-success text-white"
                       value="<?php echo $requisition['title']; ?>" disabled>
            </div>

            <!-- DEPARTMENT -->
            <div class="col-md-6 mb-3">
                <label class="form-label"><strong>Department</strong></label>
                <input type="text" class="form-control bg-success text-white"
                       value="<?php echo $requisition['department']; ?>" disabled>
            </div>

            <!-- AMOUNT -->
            <div class="col-md-6 mb-3">
                <label class="form-label"><strong>Amount (R)</strong></label>
                <input type="number" class="form-control bg-success text-white"
                       value="<?php echo $requisition['amount']; ?>" disabled>
            </div>

            <!-- PAYMENT TYPE -->
            <div class="col-md-6 mb-3">
                <label class="form-label"><strong>Payment Type</strong></label>
                <input type="text" class="form-control bg-success text-white"
                       value="<?php echo $requisition['payment_type']; ?>" disabled>
            </div>

            <!-- PAYABLE TO -->
            <div class="col-md-6 mb-3">
                <label class="form-label"><strong>To Whom Payable</strong></label>
                <input type="text" class="form-control bg-success text-white"
                       value="<?php echo $requisition['payable_to']; ?>" disabled>
            </div>

            <!-- DESCRIPTION -->
            <div class="col-12 mb-3">
                <label class="form-label"><strong>Requisition Description</strong></label>
                <textarea class="form-control bg-success text-white" rows="5" disabled><?php echo $requisition['description']; ?></textarea>
            </div>

            <!-- EXPENSE WITHIN BUDGET -->
            <div class="col-md-6 mb-3">
                <label class="form-label"><strong>Expense Within Approved Budget</strong></label>
                <input type="text" class="form-control bg-success text-white"
                      value="<?php echo !empty($requisition['budget_status']) ? $requisition['budget_status'] : 'Not Set'; ?>">
            </div>

            <!-- SUPPORTING DOCUMENT -->
            <div class="col-12 mb-3">
                <label class="form-label"><strong>Supporting Document</strong></label>
                <div>
                    <?php if(!empty($requisition['document'])): ?>
                        <a href="uploads/<?php echo $requisition['document']; ?>" target="_blank" class="btn btn-outline-primary">
                            View Document
                        </a>
                    <?php else: ?>
                        <span class="text-muted">No document uploaded</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- REJECTION REASON (IF EXISTS) -->
            <?php if(!empty($requisition['rejection_reason'])): ?>
            <div class="col-12 mb-3">
                <label class="form-label"><strong>Rejection Reason</strong></label>
                <textarea class="form-control bg-danger text-white" rows="3" disabled><?php echo $requisition['rejection_reason']; ?></textarea>
            </div>
            <?php endif; ?>

        </div>

        <!-- INFO BOX -->
        <div class="alert alert-info mt-3">
            This is a read-only view of the requisition. No actions can be performed here.
        </div>

    </div>

</div>
<script src="assets/js/script.js"></script>
</body>
</html>