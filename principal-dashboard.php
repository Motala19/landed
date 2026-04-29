<?php 
// =============================
// PRINCIPAL DASHBOARD
// =============================

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!in_array($_SESSION['role'], ['principal', 'admin'])) {
    header("Location: requisitions.php");
    exit;
}

$userName = $_SESSION['full_name'] ?? 'User';
$role = $_SESSION['role'];
$currentPage = basename($_SERVER['PHP_SELF']);

include 'includes/db.php';

// COUNTS
$newRequisitions = $conn->query("SELECT COUNT(*) as c FROM requisitions WHERE status='Pending' AND deleted_at IS NULL")->fetch_assoc()['c'];
$pendingRequisitions = $conn->query("SELECT COUNT(*) as c FROM requisitions WHERE status='Principal Approved' AND deleted_at IS NULL")->fetch_assoc()['c'];
$approvedRequisitions = $conn->query("SELECT COUNT(*) as c FROM requisitions WHERE status='Approved' AND deleted_at IS NULL")->fetch_assoc()['c'];
$rejectedRequisitions = $conn->query("SELECT COUNT(*) as c FROM requisitions WHERE status='Rejected' AND deleted_at IS NULL")->fetch_assoc()['c'];

$currentDate = date("l, d F Y");
$currentTime = date("H:i:s");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Principal Dashboard</title>
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
            <h3 class="mb-1">Principal Dashboard</h3>
            <small class="text-muted"><?php echo $currentDate . " | " . $currentTime; ?></small>
        </div>
        
        <!-- Right Side: User Info + Buttons -->
        <div class="d-flex align-items-center gap-4">
            <div class="text-end">
                <strong class="d-block fs-5"><?php echo htmlspecialchars($userName); ?></strong>
                <small class="text-muted"><?php echo ucfirst($role); ?></small>
            </div>
            
            <div class="vr text-secondary" style="height: 40px;"></div>
            
            <!-- Buttons -->
            <div class="d-flex gap-2">
                
                <a href="requisitions.php" class="btn btn-primary px-4 py-2">
                    <i class="fas fa-plus me-2"></i> My Requisitions
                </a>
            </div>
        </div>
    </div>

    <!-- Border Line -->
    <hr class="mb-4">

    <!-- Requisitions Title -->
    <h5 class="mb-4 text-dark">Requisitions</h5>

    <div class="accordion" id="principalAccordion">

    <div class="row">

        <!-- NEW -->
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-primary dashboard-card"
                 data-bs-toggle="collapse" data-bs-target="#newReq"
                 data-bs-parent="#principalAccordion">
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
                 data-bs-parent="#principalAccordion">
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
                 data-bs-parent="#principalAccordion">
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
                 data-bs-parent="#principalAccordion">
                <div class="card-body">
                    <h6>Rejected</h6>
                    <h3><?php echo $rejectedRequisitions; ?></h3>
                </div>
            </div>
        </div>

    </div>

    <!-- ================= NEW ================= -->
    <div id="newReq" class="collapse" data-bs-parent="#principalAccordion">
    <div class="card card-body mt-2">
    <h5>New (From Finance)</h5>
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
    $result = $conn->query("SELECT * FROM requisitions WHERE status='Pending' AND deleted_by_principal = 0 ORDER BY id DESC");
    while($r = $result->fetch_assoc()):
    ?>
    <tr>
    <td><?php echo $r['requisition_number']; ?></td>
    <td><?php echo $r['title']; ?></td>
    <td><?php echo $r['department']; ?></td>
    <td><?php echo $r['created_by']; ?></td>
    <td><?php echo date("d M Y H:i", strtotime($r['created_at'])); ?></td>
    <td>
    <a href="principal-verification.php?id=<?php echo $r['id']; ?>" class="btn btn-sm btn-primary">Review</a>
    </td>
    </tr>
    <?php endwhile; ?>
    </tbody>
    </table>
    </div>
    </div>

    <!-- ================= PENDING ================= -->
    <div id="pendingReq" class="collapse" data-bs-parent="#principalAccordion">
    <div class="card card-body mt-2">
    <h5>Waiting for Treasurer</h5>
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
    $result = $conn->query("SELECT * FROM requisitions WHERE status='Principal Approved' AND deleted_by_principal = 0 ORDER BY id DESC");
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

    <!-- ================= APPROVED ================= -->
    <div id="approvedReq" class="collapse" data-bs-parent="#principalAccordion">
    <div class="card card-body mt-2">
    <h5>Fully Approved (Treasurer)</h5>
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
    $result = $conn->query("SELECT * FROM requisitions WHERE status='Approved' AND deleted_by_principal = 0 ORDER BY id DESC");
    while($r = $result->fetch_assoc()):
    ?>
    <tr>
    <td><?php echo $r['requisition_number']; ?></td>
    <td><?php echo $r['title']; ?></td>
    <td><?php echo $r['department']; ?></td>
    <td><?php echo $r['created_by']; ?></td>
    <td><?php echo date("d M Y H:i", strtotime($r['created_at'])); ?></td>
    <td>
    <a href="view-requisition.php?id=<?php echo $r['id']; ?>&from=principal" class="btn btn-sm btn-primary">View</a>
    <a href="soft-delete.php?id=<?php echo $r['id']; ?>&role=principal" 
       class="btn btn-sm btn-danger" onclick="return confirm('Remove from principal view?')">Delete</a>
    </td>
    </tr>
    <?php endwhile; ?>
    </tbody>
    </table>
    </div>
    </div>

    <!-- ================= REJECTED ================= -->
    <div id="rejectedReq" class="collapse" data-bs-parent="#principalAccordion">
    <div class="card card-body mt-2">
    <h5>Rejected</h5>
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
    $result = $conn->query("SELECT * FROM requisitions WHERE status='Rejected' AND deleted_by_principal = 0 ORDER BY id DESC");
    while($r = $result->fetch_assoc()):
    ?>
    <tr>
    <td><?php echo $r['requisition_number']; ?></td>
    <td><?php echo $r['title']; ?></td>
    <td><?php echo $r['department']; ?></td>
    <td><?php echo $r['created_by']; ?></td>
    <td><?php echo date("d M Y H:i", strtotime($r['created_at'])); ?></td>
    <td>
    <a href="view-requisition.php?id=<?php echo $r['id']; ?>&type=rejected&from=principal" class="btn btn-sm btn-primary">View</a>
    <a href="soft-delete.php?id=<?php echo $r['id']; ?>&role=principal" 
       class="btn btn-sm btn-danger" onclick="return confirm('Remove from principal view?')">Delete</a>
    </td>
    </tr>
    <?php endwhile; ?>
    </tbody>
    </table>
    </div>
    </div>

    </div> <!-- end accordion -->

</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>