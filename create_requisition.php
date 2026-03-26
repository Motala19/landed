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

        <form>

        <div class="row">

            <!-- TITLE -->
            <div class="col-md-6 mb-3">
                <label class="form-label"><strong>Requisition Title</strong></label>
                <input type="text" class="form-control" required>
            </div>

            <!-- DEPARTMENT -->
            <div class="col-md-6 mb-3">
                <label class="form-label">Department</label>
                <select class="form-control" required>
                    <option value="">-- Select Department --</option>
                    <option>Academics</option>
                    <option>Sports</option>
                    <option>Administration</option>
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
            </div>

            <!-- FILE UPLOAD -->
            <div class="col-12 mb-3">
                <label class="form-label">Supporting Document</label>
                <input type="file" class="form-control">
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