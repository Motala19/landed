<?php 
session_start();

include 'includes/db.php';

// COUNTS
$totalRequisitions = $conn->query("SELECT COUNT(*) as c FROM requisitions")->fetch_assoc()['c'];
$newRequisitions = $conn->query("SELECT COUNT(*) as c FROM requisitions WHERE status='New'")->fetch_assoc()['c'];
$pendingRequisitions = $conn->query("SELECT COUNT(*) as c FROM requisitions WHERE status='Pending Principal'")->fetch_assoc()['c'];
$approvedRequisitions = $conn->query("SELECT COUNT(*) as c FROM requisitions WHERE status='Approved'")->fetch_assoc()['c'];
$rejectedRequisitions = $conn->query("SELECT COUNT(*) as c FROM requisitions WHERE status='Rejected'")->fetch_assoc()['c'];

$userName = "Finance User";

// Requisition stats
$newRequisitions ;
$pendingRequisitions ;
$approvedRequisitions ;
$rejectedRequisitions;

// Quote stats
$totalQuotes = 15;
$pendingQuotes = 3;
$approvedQuotes = 8;
$rejectedQuotes = 4;

$currentDate = date("l, d F Y");
$currentTime = date("H:i:s");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Finance Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<div class="container-fluid">
<div class="row">

<?php include 'includes/sidebar.php'; ?>

<div class="col-lg-10 p-4">

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3>Finance Dashboard</h3>
        <small><?php echo $currentDate . " | " . $currentTime; ?></small>
    </div>

    <div class="text-end">
        <strong><?php echo $userName; ?></strong><br>
        <small class="text-muted">Finance</small>
    </div>
</div>

<!-- ========================= -->
<!-- 🔵 REQUISITIONS SECTION -->
<!-- ========================= -->
<h5 class="mb-3">Requisitions</h5>

<div class="accordion" id="reqAccordion">

<div class="row">

    <!-- TOTAL -->
    <div class="col-md-3 mb-3">
        
        <div class="card text-white bg-primary shadow-sm dashboard-card status-total"
             data-bs-toggle="collapse" data-bs-target="#totalReq"
             data-bs-parent="#reqAccordion">
            <div class="card-body">
                <h6>New</h6>
                <h3><?php echo $newRequisitions; ?></h3>
            </div>
        </div>
    </div>

    <!-- PENDING -->
    <div class="col-md-3 mb-3">
        
        <div class="card text-white bg-warning shadow-sm dashboard-card status-pending"
             data-bs-toggle="collapse" data-bs-target="#pendingReq"
             data-bs-parent="#reqAccordion">
            <div class="card-body">
                <h6>Pending</h6>
                <h3><?php echo $pendingRequisitions; ?></h3>
            </div>
        </div>
    </div>

    <!-- APPROVED -->
    <div class="col-md-3 mb-3">
        
        <div class="card text-white bg-success dashboard-card status-approved" 
             data-bs-toggle="collapse" data-bs-target="#approvedReq"
             data-bs-parent="#reqAccordion">
            <div class="card-body">
                <h6>Approved</h6>
                <h3><?php echo $approvedRequisitions; ?></h3>
            </div>
        </div>
    </div>

    <!-- REJECTED -->
    <div class="col-md-3 mb-3">
        
        <div class="card text-white bg-danger dashboard-card status-rejected"
             data-bs-toggle="collapse" data-bs-target="#rejectedReq"
             data-bs-parent="#reqAccordion">
            <div class="card-body">
                <h6>Rejected</h6>
                <h3><?php echo $rejectedRequisitions; ?></h3>
            </div>
        </div>
    </div>

</div>

<!-- EXPANSIONS (FULL WIDTH BELOW ROW) -->

<!-- TOTAL TABLE -->
<div id="totalReq" class="collapse" data-bs-parent="#reqAccordion">
    <div class="card card-body mt-2">
        <h5>New Requisitions</h5>

        <table class="table">
            <thead>
                <tr>
                    <th>Number</th>
                    <th>Title</th>
                    <th>Department</th>
                    <th>Created By</th>
                    <th>Document</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

            <?php
            $result = $conn->query("SELECT * FROM requisitions WHERE status='New' ORDER BY id DESC");

            while($r = $result->fetch_assoc()):
            ?>
                <tr>
                    <td><?php echo $r['requisition_number']; ?></td>
                    <td><?php echo $r['title']; ?></td>
                    <td><?php echo $r['department']; ?></td>
                    <td><?php echo $r['created_by']; ?></td>

                    <td>
                        <?php if (!empty($r['document'])): ?>
                            <a href="uploads/<?php echo $r['document']; ?>" target="_blank">View</a>
                        <?php else: ?>
                            <span class="text-muted">None</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <a href="finance-verification.php?id=<?php echo $r['id']; ?>" class="btn btn-sm btn-primary">
                            View
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>

            </tbody>
        </table>
    </div>
</div>

<!-- PENDING TABLE -->
<div id="pendingReq" class="collapse" data-bs-parent="#reqAccordion">
    <div class="card card-body mt-2">
        <h5>Pending Principal</h5>

        <table class="table">
            <thead>
                <tr>
                    <th>Number</th>
                    <th>Title</th>
                    <th>Department</th>
                    <th>Created By</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

            <?php
            $result = $conn->query("SELECT * FROM requisitions WHERE status='Pending Principal' ORDER BY id DESC");

            while($r = $result->fetch_assoc()):
            ?>
                <tr>
                    <td><?php echo $r['requisition_number']; ?></td>
                    <td><?php echo $r['title']; ?></td>
                    <td><?php echo $r['department']; ?></td>
                    <td><?php echo $r['created_by']; ?></td>

                    <td>
                        <a href="finance-verification.php?id=<?php echo $r['id']; ?>" class="btn btn-sm btn-primary">
                            View
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>

            </tbody>
        </table>
    </div>
</div>

<!-- APPROVED TABLE -->
<div id="approvedReq" class="collapse" data-bs-parent="#reqAccordion">
    <div class="card card-body mt-2">
        <h5>Approved Requisitions</h5>

        <table class="table">
            <tbody>
                <tr>
                    <td>REQ-003</td>
                    <td>Computers</td>
                    <td>Admin</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- REJECTED TABLE -->
<div id="rejectedReq" class="collapse" data-bs-parent="#reqAccordion">
    <div class="card card-body mt-2">
        <h5>Rejected Requisitions</h5>

        <table class="table">
            <tbody>
                <tr>
                    <td>REQ-004</td>
                    <td>Chairs</td>
                    <td>Maintenance</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

</div>

<!-- ========================= -->
<!-- 🔴 QUOTES SECTION -->
<!-- ========================= -->
<h5 class="mb-3">Quotes</h5>

<div class="row mb-4">

    <div class="col-md-3 mb-3">
        <a href="finance-quotes.php" class="text-decoration-none">
            <div class="card text-white bg-primary shadow-sm dashboard-card">
                <div class="card-body">
                    <h6>Total</h6>
                    <h3><?php echo $totalQuotes; ?></h3>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-3 mb-3">
        <a href="finance-quotes.php" class="text-decoration-none">
            <div class="card text-white bg-warning shadow-sm dashboard-card">
                <div class="card-body">
                    <h6>Pending</h6>
                    <h3><?php echo $pendingQuotes; ?></h3>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-3 mb-3">
        <a href="finance-quotes.php" class="text-decoration-none">
            <div class="card text-white bg-success shadow-sm dashboard-card">
                <div class="card-body">
                    <h6>Approved</h6>
                    <h3><?php echo $approvedQuotes; ?></h3>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-3 mb-3">
        <a href="finance-quotes.php" class="text-decoration-none">
            <div class="card text-white bg-danger shadow-sm dashboard-card">
                <div class="card-body">
                    <h6>Rejected</h6>
                    <h3><?php echo $rejectedQuotes; ?></h3>
                </div>
            </div>
        </a>
    </div>

</div>

</div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- OPTIONAL HOVER EFFECT -->
<style>
.dashboard-card {
    transition: 0.3s;
}
.dashboard-card:hover {
    transform: scale(1.05);
}
</style>

</body>
</html>