<?php
session_start();
date_default_timezone_set('Africa/Johannesburg');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Only Admin can access
if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$userName = $_SESSION['full_name'] ?? 'Admin';
$role = $_SESSION['role'];
$currentPage = basename($_SERVER['PHP_SELF']);

include 'includes/db.php';

// =====================
// SUMMARY COUNTS
// =====================
$newCount = $conn->query("SELECT COUNT(*) as c FROM requisitions WHERE status='New'")->fetch_assoc()['c'];
$pendingCount = $conn->query("SELECT COUNT(*) as c FROM requisitions WHERE status='Pending'")->fetch_assoc()['c'];
$principalApproved = $conn->query("SELECT COUNT(*) as c FROM requisitions WHERE status='Principal Approved'")->fetch_assoc()['c'];
$approvedCount = $conn->query("SELECT COUNT(*) as c FROM requisitions WHERE status IN ('Approved','Paid')")->fetch_assoc()['c'];
$rejectedCount = $conn->query("SELECT COUNT(*) as c FROM requisitions WHERE status='Rejected'")->fetch_assoc()['c'];
$totalUsers = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];

$currentDate = date("l, d F Y");
$currentTime = date("H:i");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-1">Admin Dashboard</h3>
                <small class="text-muted"><?php echo $currentDate . " | " . $currentTime; ?></small>
            </div>

            <div class="text-end">
                <strong class="d-block fs-5"><?php echo htmlspecialchars($userName); ?></strong>
                <small class="text-muted">Administrator</small>
            </div>
        </div>

        <hr class="mb-4">

        <!-- SUMMARY CARDS -->
        <div class="row g-3 mb-5">
            <div class="col-md-2">
                <div class="card bg-primary text-white shadow-sm">
                    <div class="card-body text-center">
                        <h3 class="mb-0"><?php echo $newCount; ?></h3>
                        <small>New</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-warning text-white shadow-sm">
                    <div class="card-body text-center">
                        <h3 class="mb-0"><?php echo $pendingCount; ?></h3>
                        <small>Pending</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-info text-white shadow-sm">
                    <div class="card-body text-center">
                        <h3 class="mb-0"><?php echo $principalApproved; ?></h3>
                        <small>Principal Approved</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-success text-white shadow-sm">
                    <div class="card-body text-center">
                        <h3 class="mb-0"><?php echo $approvedCount; ?></h3>
                        <small>Approved / Paid</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-danger text-white shadow-sm">
                    <div class="card-body text-center">
                        <h3 class="mb-0"><?php echo $rejectedCount; ?></h3>
                        <small>Rejected</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-dark text-white shadow-sm">
                    <div class="card-body text-center">
                        <h3 class="mb-0 text-white shadow-sm"><?php echo $totalUsers; ?></h3>
                        <small>Total Users</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- QUICK ACCESS TO ROLE DASHBOARDS -->
        <h5 class="mb-3">Go to Role Dashboards</h5>

        <div class="row g-4">
            <!-- Finance -->
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-cash-stack display-4 text-primary mb-3"></i>
                        <h5>Finance Dashboard</h5>
                        <p class="text-muted">Verify new requisitions, manage payments and users</p>
                        <a href="finance-dashboard.php" class="btn btn-primary">Open Finance</a>
                    </div>
                </div>
            </div>

            <!-- Principal -->
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-person-badge display-4 text-warning mb-3"></i>
                        <h5>Principal Dashboard</h5>
                        <p class="text-muted">Review and approve requisitions</p>
                        <a href="principal-dashboard.php" class="btn btn-warning">Open Principal</a>
                    </div>
                </div>
            </div>

            <!-- Treasurer -->
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-bank display-4 text-success mb-3"></i>
                        <h5>Treasurer Dashboard</h5>
                        <p class="text-muted">Final approval of requisitions</p>
                        <a href="treasurer-dashboard.php" class="btn btn-success">Open Treasurer</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- EXTRA ADMIN LINKS -->
        <div class="row g-4 mt-2">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">Manage Users</h5>
                            <small class="text-muted">Create, reset or delete users</small>
                        </div>
                        <a href="manage-users.php" class="btn btn-outline-danger">Manage Users</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">Audit Reports</h5>
                            <small class="text-muted">View system activity log</small>
                        </div>
                        <a href="reports.php" class="btn btn-outline-dark">View Reports</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>