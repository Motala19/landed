<?php 
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Use the correct session value
$userName = $_SESSION['full_name'] ?? 'Unknown User';
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Requisition</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="bg-light">

<div class="container mt-5">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <a href="requisitions.php" class="btn btn-secondary mb-2">← Back</a>
            <h3>Create Requisition</h3>
        </div>

        <div class="text-end">
            <strong><?php echo htmlspecialchars($userName); ?></strong><br>
            <small class="text-muted"><?= ucfirst($role) ?></small>
        </div>

    </div>

    <!-- FORM -->
    <div class="card shadow-sm p-4">

       <form action="save-requisition.php" method="POST" enctype="multipart/form-data">

        <div class="row">

            <!-- TITLE -->
            <div class="col-md-6 mb-3">
                <label class="form-label"><strong>Requisition Title</strong></label>
                <input type="text" class="form-control" name="title" placeholder="Enter requisition title" required>
            </div>

            <!-- DEPARTMENT -->
            <div class="col-md-6 mb-3">
                <label class="form-label"><strong>Department</strong></label>
                <select class="form-control" name="department" required>
                    <option value="">-- Select Department --</option>
                    <option>Academics</option>
                    <option>Sports</option>
                    <option>Administration</option>
                    <option>LTSM</option>
                    <option>Maintenance</option>
                </select>
            </div>
            

            <!-- AMOUNT -->
            <div class="col-md-6 mb-3">
                <label class="form-label"><strong>Amount (R)</strong></label>
                <input type="number" class="form-control" name="amount" placeholder="Enter amount in Rands" min="0" required>
            </div>
            

            <!-- PAYMENT TYPE -->
            <div class="col-md-6 mb-3">
                <label class="form-label"><strong>Payment Type</strong></label>
                <select class="form-control" name="payment_type" required>
                    <option value="">-- Select Option --</option>
                    <option value="Cash">Cash</option>
                    <option value="Card">Card</option>
                    <option value="Eft">Eft</option>
                    <option value="Online">Online</option>
                </select>
            </div>
            
            <div class="col-md-6 mb-3">
                <label class="form-label"><strong>TO WHOM PAYABLE</strong></label>
                <input type="text" class="form-control" name="payable_to" placeholder="Enter the name of the person or entity" required>
            </div>

            <!-- DESCRIPTION -->
            <div class="col-12 mb-3">
                <label class="form-label"><strong>Requisition Description</strong></label>
                <textarea class="form-control" name="description" rows="5" placeholder="Provide a detailed description..." required></textarea>
            </div>

            <!-- FILE UPLOAD -->
            <div class="col-12 mb-3">
                <label class="form-label"><strong>Supporting Document</strong></label>
                <input type="file" name="document" class="form-control">
                <small class="text-muted">Upload any supporting document (PDF, DOCX, Image)</small>
            </div>

        </div>

        <!-- DISCLAIMER -->
        <div class="alert alert-warning mt-3">
           By clicking submit, you confirm that the information provided is true and correct, and you acknowledge responsibility for this requisition.
        </div>

        <!-- BUTTONS -->
        <div class="d-flex justify-content-end mt-3">
            <a href="requisitions.php" class="btn btn-secondary me-2">Cancel</a>
            <button type="submit" class="btn btn-primary">Submit Requisition</button>
        </div>

        </form>

    </div>

</div>

</body>
</html>