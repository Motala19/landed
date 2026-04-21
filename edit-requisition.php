<?php
session_start();
include 'includes/db.php';

$id = $_GET['id'];

// GET DATA
$result = $conn->query("SELECT * FROM requisitions WHERE id = $id");
$r = $result->fetch_assoc();

?>
<?php if($r['status'] != 'Rejected'): ?>
    <div class="alert alert-danger">
        You can only edit rejected requisitions.
    </div>
<?php endif; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Requisition</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="requisitions.php" class="btn btn-secondary mb-2">← Back</a>
            <h3>Edit Requisition</h3>
        </div>
    </div>

    <!-- FORM -->
    <div class="card p-4 shadow-sm">

        <form action="update-requisition.php" method="POST" enctype="multipart/form-data">

        <input type="hidden" name="id" value="<?php echo $r['id']; ?>">

        <div class="row">

            <div class="col-md-6 mb-3">
                <label><strong>Title</strong></label>
                <input type="text" name="title" class="form-control"
                       value="<?php echo $r['title']; ?>">
            </div>

            <div class="col-md-6 mb-3">
                <label><strong>Department</strong></label>
                <select class="form-control" name="department" required>
                    <option value=""><?php echo $r['department']; ?></option>
                    <option>Academics</option>
                    <option>Sports</option>
                    <option>Administration</option>
                     <option>LTSM</option>
                      <option>Maintanance</option>
                </select>
                       
            </div>

            <div class="col-md-6 mb-3">
                <label><strong>Amount</strong></label>
                <input type="number" name="amount" class="form-control"
                       value="<?php echo $r['amount']; ?>">
            </div>

            <div class="col-md-6 mb-3">
                <label><strong>Payment Type</strong></label>
                <input type="text" name="payment_type" class="form-control"
                       value="<?php echo $r['payment_type']; ?>">
            </div>

            <div class="col-md-6 mb-3">
                <label><strong>Payable To</strong></label>
                <input type="text" name="payable_to" class="form-control"
                       value="<?php echo $r['payable_to']; ?>">
            </div>

            <div class="col-12 mb-3">
                <label><strong>Description</strong></label>
                <textarea name="description" class="form-control"><?php echo $r['description']; ?></textarea>
            </div>

            <div class="col-12 mb-3">
                <label><strong>Current Document</strong></label><br>
                <a href="uploads/<?php echo $r['document']; ?>" target="_blank">View File</a>
            </div>

            <div class="col-12 mb-3">
                <label><strong>Replace Document</strong></label>
                <input type="file" name="document" class="form-control">
            </div>

        </div>

        <!-- BUTTONS -->
        

        <div class="d-flex justify-content-end mt-3">
    <a href="requisitions.php" class="btn btn-secondary me-2">Cancel</a>

    <button type="submit" name="action" value="update" class="btn btn-warning me-2">
        Save Changes
    </button>

    <button type="submit" name="action" value="resubmit" class="btn btn-success">
        Resubmit
    </button>
</div>

        </form>

    </div>

</div>

</body>
</html>