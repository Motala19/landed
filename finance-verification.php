<?php
session_start();

// 🚫 Prevent browser caching (VERY IMPORTANT)
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
 


$userName = "Finance User";

// Dummy pre-filled data from staff requisition
include 'includes/db.php';

$id = $_GET['id'] ?? 0;

$result = $conn->query("SELECT * FROM requisitions WHERE id = $id");
$requisition = $result->fetch_assoc();




?>

<!DOCTYPE html>
<html>
<head>
    <title>Finance Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="finance-dashboard.php" class="btn btn-secondary mb-2">← Back</a>
            <h3>Finance Verification</h3>
        </div>

        <div class="text-end">
            <strong><?php echo $userName; ?></strong><br>
            <small class="text-muted">Finance</small>
        </div>
    </div>

    <!-- FORM -->
    <div class="card shadow-sm p-4">

        <form method="POST" action="finance-process.php">

        <div class="row">
        <div>
            <input type="hidden" name="id" value="<?php echo $requisition['id']; ?>">
                </div>
            <!-- STAFF MEMBER -->
            <!-- STAFF MEMBER (Auto-filled / Verified) -->
                <div class="col-md-6 mb-3">
                 <label class="form-label"><strong>Submitted By</strong></label>
                <input type="text" class="form-control bg-success text-white" value="<?php echo $requisition['created_by'] ?? ''; ?>" readonly>
                </div>
                <div>
            <input type="hidden" name="id" value="<?php echo $requisition['id']; ?>">
                </div>
            <!-- TITLE -->
            <div class="col-md-6 mb-3">
                <label class="form-label"><strong>Requisition Title</strong></label>
                <input type="text" class="form-control bg-success text-white" value="<?php echo $requisition['title']; ?>" disabled>
            </div>

            <!-- DEPARTMENT -->
            <div class="col-md-6 mb-3">
                <label class="form-label"><strong>Department</strong></label>
                <input type="text" class="form-control bg-success text-white" value="<?php echo $requisition['department']; ?>" disabled>
            </div>
            
            <!-- AMOUNT -->
            <div class="col-md-6 mb-3">
                <label class="form-label"><strong>Amount (R)</strong></label>
                <input type="number" class="form-control bg-success text-white" value="<?php echo $requisition['amount']; ?>" disabled>
            </div>
            
            <!-- PAYMENT TYPE -->
            <div class="col-md-6 mb-3">
                <label class="form-label"><strong>Payment Type</strong></label>
                <input type="text" class="form-control bg-success text-white" value="<?php echo $requisition['payment_type']; ?>" disabled>
            </div>
            
            <!-- PAYABLE TO -->
            <div class="col-md-6 mb-3">
                <label class="form-label"><strong>To Whom Payable</strong></label>
                <input type="text" class="form-control bg-success text-white" value="<?php echo $requisition['payable_to']; ?>" disabled>
            </div>

            <!-- DESCRIPTION -->
            <div class="col-12 mb-3">
                <label class="form-label"><strong>Requisition Description</strong></label>
                <textarea class="form-control bg-success text-white" rows="5" disabled><?php echo $requisition['description']; ?></textarea>
            </div>

            <!-- EXPENSE WITHIN BUDGET -->
            <div class="col-md-6 mb-3">
                <label class="form-label"><strong>Expense Within the Approved Budget?</strong></label>
                
                <select name="budget_check" class="form-control" required>
                <option value="">-- Select Budget Status --</option>
                <option value="Yes">Yes</option>
             <option value="No">No</option>
</select>
            </div>

            <!-- SUPPORTING DOCUMENT -->
            <div class="col-12 mb-3">
                <label class="form-label"><strong>Supporting Document</strong></label>
                <div>
                    <a href="uploads/<?php echo $requisition['document']; ?>" target="_blank" class="btn btn-outline-primary">
                        View Document
                    </a>
                </div>
            </div>

            <!-- REJECTION REASON -->
            <div class="col-12 mb-3">
                <label class="form-label"><strong>Reason for Rejection</strong></label>
                <textarea name="reason" class="form-control" rows="3" placeholder="Fill this if you reject the requisition"></textarea>
            </div>

        </div>

        <!-- DISCLAIMER -->
        <div class="alert alert-warning mt-3">
            You are viewing a requisition submitted by staff. All information shown is as provided by the submitter.
        </div>

        <!-- BUTTONS -->
        <div class="d-flex justify-content-end mt-3">
            <button type="submit" name="action" value="reject" class="btn btn-danger me-2">Reject</button>
            <button type="submit" name="action" value="verify" class="btn btn-success">Request</button>
            
        </div>

        </form>

    </div>

</div>

</body>
</html>