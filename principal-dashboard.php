<?php 
// =============================
// PRINCIPAL DASHBOARD
// =============================
session_start();
include 'includes/db.php';

$userName = "Principal User";

// COUNTS
$pendingRequisitions = $conn->query("SELECT COUNT(*) as c FROM requisitions WHERE status='Pending Principal'")->fetch_assoc()['c'];
$approvedRequisitions = $conn->query("SELECT COUNT(*) as c FROM requisitions WHERE status='Approved'")->fetch_assoc()['c'];
$rejectedRequisitions = $conn->query("SELECT COUNT(*) as c FROM requisitions WHERE status='Rejected'")->fetch_assoc()['c'];

$currentDate = date("l, d F Y");
$currentTime = date("H:i:s");
?>

<!DOCTYPE html>
<html>
<head>
<title>Principal Dashboard</title>
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
        <h3>Principal Dashboard</h3>
        <small><?php echo $currentDate . " | " . $currentTime; ?></small>
    </div>

    <div class="text-end">
        <strong><?php echo $userName; ?></strong><br>
        <small class="text-muted">Principal</small>
    </div>
</div>

<h5 class="mb-3">Requisitions</h5>

<div class="accordion" id="principalAccordion">

<div class="row">

<!-- PENDING -->
<div class="col-md-4 mb-3">
<div class="card text-white bg-warning dashboard-card"
     data-bs-toggle="collapse" data-bs-target="#pendingReq"
     data-bs-parent="#principalAccordion">
    <div class="card-body">
        <h6>Pending</h6>
        <h3><?php echo $pendingRequisitions; ?></h3>
    </div>
</div>
</div>

<!-- APPROVED -->
<div class="col-md-4 mb-3">
<div class="card text-white bg-success dashboard-card"
     data-bs-toggle="collapse" data-bs-target="#approvedReq"
     data-bs-parent="#principalAccordion">
    <div class="card-body">
        <h6>Approved</h6>
        <h3><?php echo $approvedRequisitions; ?></h3>
    </div>
</div>
</div>

<!-- REJECTED -->
<div class="col-md-4 mb-3">
<div class="card text-white bg-danger dashboard-card"
     data-bs-toggle="collapse" data-bs-target="#rejectedReq"
     data-bs-parent="#principalAccordion">
    <div class="card-body">
        <h6>Rejected</h6>
        <h3><?php echo $rejectedRequisitions; ?></h3>
    </div>
</div>
</div>

</div>

<!-- PENDING -->
<div id="pendingReq" class="collapse" data-bs-parent="#principalAccordion">
<div class="card card-body mt-2">

<h5>Pending Principal Approval</h5>

<table class="table">
<thead>
<tr>
<th>Number</th>
<th>Title</th>
<th>Department</th>
<th>Created By</th>
<th>Date</th>
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
<td><?php echo date("d M Y H:i", strtotime($r['created_at'])); ?></td>

<td>
<a href="principal-verification.php?id=<?php echo $r['id']; ?>" class="btn btn-sm btn-primary">
Verify
</a>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

</div>
</div>

<!-- APPROVED -->
<!-- APPROVED TABLE -->
<div id="approvedReq" class="collapse" data-bs-parent="#principalAccordion">
<div class="card card-body mt-2">
<h5>Approved Requisitions</h5>

<table class="table">
<thead>
<tr>
    <th>Number</th>
    <th>Title</th>
    <th>Department</th>
    <th>Created By</th>
    <th>Date Created</th>
    <th>Action</th>
</tr>
</thead>

<tbody>

<?php
$result = $conn->query("SELECT * FROM requisitions WHERE status='Approved' ORDER BY id DESC");

while($r = $result->fetch_assoc()):
?>

<tr>
<td><?php echo $r['requisition_number']; ?></td>
<td><?php echo $r['title']; ?></td>
<td><?php echo $r['department']; ?></td>
<td><?php echo $r['created_by']; ?></td>
<td><?php echo date("d M Y H:i", strtotime($r['created_at'])); ?></td>

<td>
<a href="principal-view-requisition.php?id=<?php echo $r['id']; ?>" class="btn btn-sm btn-primary">
View
</a>
</td>
</tr>

<?php endwhile; ?>

</tbody>
</table>

</div>
</div>

<!-- REJECTED -->
<div id="rejectedReq" class="collapse" data-bs-parent="#principalAccordion">
<div class="card card-body mt-2">

<h5>Rejected Requisitions</h5>

<table class="table">
<thead>
<tr>
<th>Number</th>
<th>Title</th>
<th>Department</th>
<th>Created By</th>
<th>Date</th>
<th>Rejected By</th>
<th>Action</th>
</tr>
</thead>

<tbody>
<?php
$result = $conn->query("SELECT * FROM requisitions WHERE status='Rejected' ORDER BY id DESC");

while($r = $result->fetch_assoc()):
?>
<tr>
<td><?php echo $r['requisition_number']; ?></td>
<td><?php echo $r['title']; ?></td>
<td><?php echo $r['department']; ?></td>
<td><?php echo $r['created_by']; ?></td>
<td><?php echo date("d M Y H:i", strtotime($r['created_at'])); ?></td>
<td><?php echo $r['rejected_by'] ?? 'Unknown'; ?></td>

<td>
<a href="principal-verification.php?id=<?php echo $r['id']; ?>" class="btn btn-sm btn-primary">
View
</a>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

</div>
</div>

</div>

</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>