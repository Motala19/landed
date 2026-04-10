<?php 
session_start();

$userName = "Motala Godfrey";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Requisition</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
            <strong><?php echo $userName; ?></strong><br>
            <small class="text-muted">Staff</small>
        </div>

    </div>

    <!-- FORM -->
    <div class="card shadow-sm p-4">

<<<<<<< HEAD
       <form action="save-requisition.php" method="POST" enctype="multipart/form-data">
=======
        <form>
>>>>>>> 7ab4caea6570dc3165596da215d94ef01da59283

        <div class="row">

            <!-- TITLE -->
            <div class="col-md-6 mb-3">
                <label class="form-label"><strong>Requisition Title</strong></label>
<<<<<<< HEAD
                <input type="text" class="form-control" name="title" placeholder="Enter requisition title" required>
=======
                <input type="text" class="form-control" required>
>>>>>>> 7ab4caea6570dc3165596da215d94ef01da59283
            </div>

            <!-- DEPARTMENT -->
            <div class="col-md-6 mb-3">
<<<<<<< HEAD
                <label class="form-label"><strong>Department</strong></label>
                <select class="form-control" name="department" required>
=======
                <label class="form-label">Department</label>
                <select class="form-control" required>
>>>>>>> 7ab4caea6570dc3165596da215d94ef01da59283
                    <option value="">-- Select Department --</option>
                    <option>Academics</option>
                    <option>Sports</option>
                    <option>Administration</option>
<<<<<<< HEAD
                     <option>LTSM</option>
                      <option>Maintanance</option>
                </select>
            </div>
            

            <!-- AMOUNT -->
            <div class="col-md-6 mb-3">
                <label class="form-label"><strong>Amount (R)</strong></label>
                <input type="number" class="form-control" name="amount" placeholder="Enter amount in Rands" min="0" required>
            </div>
            

            <!-- BUDGET TO BE MOVED TO FINANCE MODULE-->
            <div class="col-md-6 mb-3">
                <label class="form-label"><strong>Payment Type</strong></label>
                <select class="form-control" name="payment_type" required>
                    <option value="">-- Select Option --</option>
                    <option value="Cash">Cash</option>
                    <option value="Card">Card</option>
                     <option value="Eft">Eft</option>
                     <option value="Eft">Online</option>
                </select>
            </div>
            
            <div class="col-md-6 mb-3">
                <label class="form-label"><strong>TO WHOM PAYABLE</strong></label>
                <input type="text" class="form-control" name="payable_to" placeholder="Enter the name of the person or entity" min="0" required>
            </div>

            <!-- DESCRIPTION -->
            <div class="col-12 mb-3">
                <label class="form-label"><strong>Requisition Description</strong></label>
                <textarea class="form-control" name="description" rows="5" placeholder="Provide a detailed description..." required></textarea>
=======
                </select>
            </div>

            <!-- AMOUNT -->
            <div class="col-md-6 mb-3">
                <label class="form-label">Amount (R)</label>
                <input type="number" class="form-control" placeholder="Enter amount in Rands" min="0" required>
            </div>

            <!-- BUDGET -->
            <div class="col-md-6 mb-3">
                <label class="form-label">EXPENSE WITHIN THE APPROVED BUDGET</label>
                <select class="form-control" required>
                    <option value="">-- Select Option --</option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
            </div>

            <!-- DESCRIPTION -->
            <div class="col-12 mb-3">
                <label class="form-label">Requisition Description</label>
                <textarea class="form-control" rows="5" placeholder="Provide a detailed description..." required></textarea>
>>>>>>> 7ab4caea6570dc3165596da215d94ef01da59283
            </div>

            <!-- FILE UPLOAD -->
            <div class="col-12 mb-3">
<<<<<<< HEAD
                <label class="form-label"><strong>Supporting Document</strong></label>
                <input type="file" name="document" class="form-control">
=======
                <label class="form-label">Supporting Document</label>
                <input type="file" class="form-control">
>>>>>>> 7ab4caea6570dc3165596da215d94ef01da59283
                <small class="text-muted">Upload any supporting document (PDF, DOCX, Image)</small>
            </div>

        </div>

        <!-- DISCLAIMER -->
        <div class="alert alert-warning mt-3">
<<<<<<< HEAD
           By clicking submit, you confirm that the information provided is true and correct, and you acknowledge responsibility for this requisition.
        </di v>
=======
            By clicking submit, you confirm that the information provided is true and correct, and you acknowledge responsibility for this requisition.
        </div>
>>>>>>> 7ab4caea6570dc3165596da215d94ef01da59283

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