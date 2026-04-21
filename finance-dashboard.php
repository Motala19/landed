<?php 
// =============================
// FINANCE DASHBOARD
// =============================

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!in_array($_SESSION['role'], ['finance', 'admin'])) {
    header("Location: requisitions.php");
    exit;
}

$userName = $_SESSION['full_name'] ?? 'User';
$role = $_SESSION['role'];
$currentPage = basename($_SERVER['PHP_SELF']);

include 'includes/db.php';

// COUNTS
$newRequisitions = $conn->query("SELECT COUNT(*) as c FROM requisitions WHERE status='New'")->fetch_assoc()['c'];
$pendingRequisitions = $conn->query("SELECT COUNT(*) as c FROM requisitions WHERE status='Pending'")->fetch_assoc()['c'];
$approvedRequisitions = $conn->query("SELECT COUNT(*) as c FROM requisitions WHERE status='Approved'")->fetch_assoc()['c'];
$rejectedRequisitions = $conn->query("SELECT COUNT(*) as c FROM requisitions WHERE status='Rejected'")->fetch_assoc()['c'];

$currentDate = date("l, d F Y");
$currentTime = date("H:i:s");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Finance Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container-fluid">
<div class="row">

<?php include 'includes/sidebar.php'; ?>

<div class="col-lg-10 p-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-1">Finance Dashboard</h3>
            <small class="text-muted"><?php echo $currentDate . " | " . $currentTime; ?></small>
        </div>
        
        <!-- Right Side -->
        <div class="d-flex align-items-center gap-4">
            <div class="text-end">
                <strong class="d-block fs-5"><?php echo htmlspecialchars($userName); ?></strong>
                <small class="text-muted"><?php echo ucfirst($role); ?></small>
            </div>
            
            <div class="vr text-secondary" style="height: 40px;"></div>
            
            <div class="d-flex gap-2">
                <a href="manage-users.php" class="btn btn-outline-danger px-4 py-2">
                    <i class="fas fa-users me-2"></i> Manage Users
                </a>
                <a href="requisitions.php" class="btn btn-primary px-4 py-2">
                    <i class="fas fa-plus me-2"></i> Create Requisition
                </a>
            </div>
        </div>
    </div>

    <!-- Border Line -->
    <hr class="mb-4">

    <h5 class="mb-4 text-dark">Requisitions</h5>

    <div class="accordion" id="reqAccordion">

    <div class="row">

        <!-- NEW -->
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-primary dashboard-card"
                 data-bs-toggle="collapse" data-bs-target="#newReq"
                 data-bs-parent="#reqAccordion">
                <div class="card-body">
                    <h6>New</h6>
                    <h3><?php echo $newRequisitions; ?></h3>
                </div>
            </div>
        </div>

        <!-- PENDING -->
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-warning dashboard-card"
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
            <div class="card text-white bg-success dashboard-card"
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
            <div class="card text-white bg-danger dashboard-card"
                 data-bs-toggle="collapse" data-bs-target="#rejectedReq"
                 data-bs-parent="#reqAccordion">
                <div class="card-body">
                    <h6>Rejected</h6>
                    <h3><?php echo $rejectedRequisitions; ?></h3>
                </div>
            </div>
        </div>

    </div>

    <!-- === YOUR ORIGINAL TABLES START HERE === -->

    <!-- NEW TABLE -->
    <div id="newReq" class="collapse" data-bs-parent="#reqAccordion">
    <div class="card card-body mt-2">
    <h5>New Requisitions</h5>
    <table class="table">
    <thead>
    <tr>
        <th>Number</th>
        <th>Title</th>
        <th>Department</th>
        <th>Created By</th>
        <th>Date Created</th>
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
    <td><?php echo date("d M Y H:i", strtotime($r['created_at'])); ?></td>
    <td>
    <?php if (!empty($r['document'])): ?>
    <a href="uploads/<?php echo $r['document']; ?>" target="_blank">View</a>
    <?php else: ?>
    <span class="text-muted">None</span>
    <?php endif; ?>
    </td>
    <td>
    <a href="finance-verification.php?id=<?php echo $r['id']; ?>" class="btn btn-sm btn-primary">Verify</a>
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
    <h5>Pending Requisitions</h5>
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
    $result = $conn->query("SELECT * FROM requisitions WHERE status='Pending' ORDER BY id DESC");
    while($r = $result->fetch_assoc()):
    ?>
    <tr>
    <td><?php echo $r['requisition_number']; ?></td>
    <td><?php echo $r['title']; ?></td>
    <td><?php echo $r['department']; ?></td>
    <td><?php echo $r['created_by']; ?></td>
    <td><?php echo date("d M Y H:i", strtotime($r['created_at'])); ?></td>
    <td>
    <a href="view-requisition.php?id=<?php echo $r['id']; ?>&type=pending" class="btn btn-sm btn-primary">View</a>
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
    <a href="view-requisition.php?id=<?php echo $r['id']; ?>&type=pending&from=finance" class="btn btn-sm btn-primary">View</a>
    <a href="finance-delete.php?id=<?php echo $r['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Permanently delete this record?')">Delete</a>
    </td>
    </tr>
    <?php endwhile; ?>
    </tbody>
    </table>
    </div>
    </div>

    <!-- REJECTED TABLE -->
    <div id="rejectedReq" class="collapse" data-bs-parent="#reqAccordion">
    <div class="card card-body mt-2">
    <h5>Rejected Requisitions</h5>
    <table class="table">
    <thead>
    <tr>
        <th>Number</th>
        <th>Title</th>
        <th>Department</th>
        <th>Created By</th>
        <th>Date Created</th>
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
    <a href="view-requisition.php?id=<?php echo $r['id']; ?>&type=rejected" class="btn btn-sm btn-primary">View</a>
    <a href="finance-delete.php?id=<?php echo $r['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Permanently delete this record?')">Delete</a>
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