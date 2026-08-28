<?php 
// =============================
// PRINCIPAL DASHBOARD
// =============================

session_start();
date_default_timezone_set('Africa/Johannesburg');

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

// =============================
// COUNTS
// =============================

$newRequisitions = $conn->query("
SELECT COUNT(*) as c FROM requisitions 
WHERE status='Pending' AND deleted_by_principal = 0
")->fetch_assoc()['c'];

$pendingRequisitions = $conn->query("
SELECT COUNT(*) as c FROM requisitions 
WHERE status='Principal Approved' AND deleted_by_principal = 0
")->fetch_assoc()['c'];

$approvedRequisitions = $conn->query("
SELECT COUNT(*) as c FROM requisitions 
WHERE status IN ('Approved', 'Paid') AND deleted_by_principal = 0
")->fetch_assoc()['c'];

$rejectedRequisitions = $conn->query("
SELECT COUNT(*) as c FROM requisitions 
WHERE status='Rejected' AND deleted_by_principal = 0
")->fetch_assoc()['c'];

$currentDate = date("l, d F Y");
$currentTime = date("H:i");

// =============================
// PAGINATION
// =============================

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;

if (!in_array($limit, [5,10,20])) {
    $limit = 5;
}

// Detect open accordion
$open = $_GET['open'] ?? '';

// NEW
$newPage = isset($_GET['new_page']) ? (int)$_GET['new_page'] : 1;
$newStart = ($newPage - 1) * $limit;
$newTotal = $conn->query("
SELECT COUNT(*) as total FROM requisitions 
WHERE status='Pending' AND deleted_by_principal = 0
")->fetch_assoc()['total'];
$newPages = ceil($newTotal / $limit);

// PENDING
$pendingPage = isset($_GET['pending_page']) ? (int)$_GET['pending_page'] : 1;
$pendingStart = ($pendingPage - 1) * $limit;
$pendingTotal = $conn->query("
SELECT COUNT(*) as total FROM requisitions 
WHERE status='Principal Approved' AND deleted_by_principal = 0
")->fetch_assoc()['total'];
$pendingPages = ceil($pendingTotal / $limit);


// APPROVED
$approvedPage = isset($_GET['approved_page']) ? (int)$_GET['approved_page'] : 1;
$approvedStart = ($approvedPage - 1) * $limit;
$approvedTotal = $conn->query("
SELECT COUNT(*) as total FROM requisitions 
WHERE status IN ('Approved', 'Paid') AND deleted_by_principal = 0
")->fetch_assoc()['total'];
$approvedPages = max(1, ceil($approvedTotal / $limit));

// REJECTED
$rejectedPage = isset($_GET['rejected_page']) ? (int)$_GET['rejected_page'] : 1;
$rejectedStart = ($rejectedPage - 1) * $limit;
$rejectedTotal = $conn->query("
SELECT COUNT(*) as total FROM requisitions 
WHERE status='Rejected' AND deleted_by_principal = 0
")->fetch_assoc()['total'];
$rejectedPages = ceil($rejectedTotal / $limit);

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

<small class="text-muted">
<?php echo $currentDate . " | " . $currentTime; ?>
</small>
</div>

<div class="d-flex align-items-center gap-4">

<div class="text-end">
<strong class="d-block fs-5">
<?php echo htmlspecialchars($userName); ?>
</strong>

<small class="text-muted">
<?php echo ucfirst($role); ?>
</small>
</div>

<div class="vr text-secondary" style="height: 40px;"></div>

<div class="d-flex gap-2">

<a href="requisitions.php" class="btn btn-primary px-4 py-2">
My Requisitions
</a>

</div>

</div>

</div>

<hr class="mb-4">

<h5 class="mb-4 text-dark">Requisitions</h5>

<div class="accordion" id="principalAccordion">

<div class="row">

<!-- NEW -->
<div class="col-md-3 mb-3">

<div class="card dashboard-card bg-primary shadow-lg"
data-bs-toggle="collapse"
data-bs-target="#newReq">

<div class="card-body position-relative">

<h2 class="fw-bold mb-1 text-white">
<?php echo $newRequisitions; ?>
</h2>

<h6 class="text-white">New</h6>

<i class="bi bi-plus-circle-fill card-icon"></i>

</div>

</div>

</div>

<!-- PENDING -->
<div class="col-md-3 mb-3">

<div class="card dashboard-card bg-warning shadow-lg"
data-bs-toggle="collapse"
data-bs-target="#pendingReq">

<div class="card-body position-relative">

<h2 class="fw-bold mb-1 text-white">
<?php echo $pendingRequisitions; ?>
</h2>

<h6 class="text-white">Pending</h6>

<i class="bi bi-hourglass-split card-icon"></i>

</div>

</div>

</div>

<!-- APPROVED -->
<div class="col-md-3 mb-3">

<div class="card dashboard-card bg-success shadow-lg"
data-bs-toggle="collapse"
data-bs-target="#approvedReq">

<div class="card-body position-relative">

<h2 class="fw-bold mb-1 text-white">
<?php echo $approvedRequisitions; ?>
</h2>

<h6 class="text-white">Approved</h6>

<i class="bi bi-check-circle-fill card-icon"></i>

</div>

</div>

</div>

<!-- REJECTED -->
<div class="col-md-3 mb-3">

<div class="card dashboard-card bg-danger shadow-lg"
data-bs-toggle="collapse"
data-bs-target="#rejectedReq">

<div class="card-body position-relative">

<h2 class="fw-bold mb-1 text-white">
<?php echo $rejectedRequisitions; ?>
</h2>

<h6 class="text-white">Rejected</h6>

<i class="bi bi-x-circle-fill card-icon"></i>

</div>

</div>

</div>

</div>

<!-- ================= NEW ================= -->

<div id="newReq"
class="collapse <?php if($open == 'newReq') echo 'show'; ?>"
data-bs-parent="#principalAccordion">

<div class="card card-body mt-2">

<div class="d-flex justify-content-between align-items-center mb-3">

<h5 class="mb-0">New (From Finance)</h5>

<form method="GET" class="d-flex align-items-center gap-2">

<input type="hidden" name="open" value="newReq">

<label class="mb-0">Show</label>

<select name="limit"
class="form-select w-auto"
onchange="this.form.submit()">

<option value="5" <?php if($limit==5) echo 'selected'; ?>>5</option>
<option value="10" <?php if($limit==10) echo 'selected'; ?>>10</option>
<option value="20" <?php if($limit==20) echo 'selected'; ?>>20</option>

</select>

<span>entries</span>

</form>

</div>

<table class="table table-bordered table-hover">

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
$result = $conn->query("
SELECT * FROM requisitions 
WHERE status='Pending' 
AND deleted_by_principal = 0
ORDER BY id DESC 
LIMIT $newStart, $limit
");

while($r = $result->fetch_assoc()):
?>

<tr>

<td><?php echo $r['requisition_number']; ?></td>
<td><?php echo $r['title']; ?></td>
<td><?php echo $r['department']; ?></td>
<td><?php echo $r['created_by']; ?></td>
<td><?php echo date("d M Y H:i", strtotime($r['created_at'])); ?></td>

<td>

<a href="principal-verification.php?id=<?php echo $r['id']; ?>"
class="btn btn-sm btn-primary">
Review
</a>

</td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

<nav class="d-flex justify-content-end">

<ul class="pagination">

<?php if($newPage > 1): ?>

<li class="page-item">
<a class="page-link"
href="?new_page=<?php echo $newPage-1; ?>&limit=<?php echo $limit; ?>&open=newReq">
Previous
</a>
</li>

<?php endif; ?>

<?php for($i=1; $i <= $newPages; $i++): ?>

<li class="page-item <?php if($i == $newPage) echo 'active'; ?>">

<a class="page-link"
href="?new_page=<?php echo $i; ?>&limit=<?php echo $limit; ?>&open=newReq">

<?php echo $i; ?>

</a>

</li>

<?php endfor; ?>

<?php if($newPage < $newPages): ?>

<li class="page-item">
<a class="page-link"
href="?new_page=<?php echo $newPage+1; ?>&limit=<?php echo $limit; ?>&open=newReq">
Next
</a>
</li>

<?php endif; ?>

</ul>

</nav>

</div>
</div>

<!-- ================= PENDING ================= -->

<div id="pendingReq"
class="collapse <?php if($open == 'pendingReq') echo 'show'; ?>"
data-bs-parent="#principalAccordion">

<div class="card card-body mt-2">

<div class="d-flex justify-content-between align-items-center mb-3">

<h5 class="mb-0">Waiting for Treasurer</h5>

<form method="GET" class="d-flex align-items-center gap-2">

<input type="hidden" name="open" value="pendingReq">

<label class="mb-0">Show</label>

<select name="limit"
class="form-select w-auto"
onchange="this.form.submit()">

<option value="5" <?php if($limit==5) echo 'selected'; ?>>5</option>
<option value="10" <?php if($limit==10) echo 'selected'; ?>>10</option>
<option value="20" <?php if($limit==20) echo 'selected'; ?>>20</option>

</select>

<span>entries</span>

</form>

</div>

<table class="table table-bordered table-hover">

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
$result = $conn->query("
SELECT * FROM requisitions 
WHERE status='Principal Approved'
AND deleted_by_principal = 0
ORDER BY id DESC 
LIMIT $pendingStart, $limit
");

while($r = $result->fetch_assoc()):
?>

<tr>

<td><?php echo $r['requisition_number']; ?></td>
<td><?php echo $r['title']; ?></td>
<td><?php echo $r['department']; ?></td>
<td><?php echo $r['created_by']; ?></td>
<td><?php echo date("d M Y H:i", strtotime($r['created_at'])); ?></td>

<td>

<a href="view-requisition.php?id=<?php echo $r['id']; ?>&type=pending"
class="btn btn-sm btn-primary">
View
</a>

</td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

<nav class="d-flex justify-content-end">

<ul class="pagination">

<?php if($pendingPage > 1): ?>

<li class="page-item">
<a class="page-link"
href="?pending_page=<?php echo $pendingPage-1; ?>&limit=<?php echo $limit; ?>&open=pendingReq">
Previous
</a>
</li>

<?php endif; ?>

<?php for($i=1; $i <= $pendingPages; $i++): ?>

<li class="page-item <?php if($i == $pendingPage) echo 'active'; ?>">

<a class="page-link"
href="?pending_page=<?php echo $i; ?>&limit=<?php echo $limit; ?>&open=pendingReq">

<?php echo $i; ?>

</a>

</li>

<?php endfor; ?>

<?php if($pendingPage < $pendingPages): ?>

<li class="page-item">
<a class="page-link"
href="?pending_page=<?php echo $pendingPage+1; ?>&limit=<?php echo $limit; ?>&open=pendingReq">
Next
</a>
</li>

<?php endif; ?>

</ul>

</nav>

</div>
</div>

<!-- ================= APPROVED ================= -->

<div id="approvedReq"
class="collapse <?php if($open == 'approvedReq') echo 'show'; ?>"
data-bs-parent="#principalAccordion">

<div class="card card-body mt-2">

<div class="d-flex justify-content-between align-items-center mb-3">

<h5 class="mb-0">Fully Approved (Treasurer)</h5>

<form method="GET" class="d-flex align-items-center gap-2">

<input type="hidden" name="open" value="approvedReq">

<label class="mb-0">Show</label>

<select name="limit"
class="form-select w-auto"
onchange="this.form.submit()">

<option value="5" <?php if($limit==5) echo 'selected'; ?>>5</option>
<option value="10" <?php if($limit==10) echo 'selected'; ?>>10</option>
<option value="20" <?php if($limit==20) echo 'selected'; ?>>20</option>

</select>

<span>entries</span>

</form>

</div>

<table class="table table-bordered table-hover">

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
$result = $conn->query("
SELECT * FROM requisitions 
WHERE status IN ('Approved', 'Paid')
AND deleted_by_principal = 0
ORDER BY id DESC 
LIMIT $approvedStart, $limit
");

while($r = $result->fetch_assoc()):
?>

<tr>

<td><?php echo $r['requisition_number']; ?></td>
<td><?php echo $r['title']; ?></td>
<td><?php echo $r['department']; ?></td>
<td><?php echo $r['created_by']; ?></td>
<td><?php echo date("d M Y H:i", strtotime($r['created_at'])); ?></td>

<td>

<a href="view-requisition.php?id=<?php echo $r['id']; ?>&from=principal"
class="btn btn-sm btn-primary">
View
</a>

<a href="soft-delete.php?id=<?php echo $r['id']; ?>&role=principal"
class="btn btn-sm btn-danger"
onclick="return confirm('Are you sure you want to delete this requisition?')">
Delete
</a>

</td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

<nav class="d-flex justify-content-end">

<ul class="pagination">

<?php if($approvedPage > 1): ?>

<li class="page-item">
<a class="page-link"
href="?approved_page=<?php echo $approvedPage-1; ?>&limit=<?php echo $limit; ?>&open=approvedReq">
Previous
</a>
</li>

<?php endif; ?>

<?php for($i=1; $i <= $approvedPages; $i++): ?>

<li class="page-item <?php if($i == $approvedPage) echo 'active'; ?>">

<a class="page-link"
href="?approved_page=<?php echo $i; ?>&limit=<?php echo $limit; ?>&open=approvedReq">

<?php echo $i; ?>

</a>

</li>

<?php endfor; ?>

<?php if($approvedPage < $approvedPages): ?>

<li class="page-item">
<a class="page-link"
href="?approved_page=<?php echo $approvedPage+1; ?>&limit=<?php echo $limit; ?>&open=approvedReq">
Next
</a>
</li>

<?php endif; ?>

</ul>

</nav>

</div>
</div>

<!-- ================= REJECTED ================= -->

<div id="rejectedReq"
class="collapse <?php if($open == 'rejectedReq') echo 'show'; ?>"
data-bs-parent="#principalAccordion">

<div class="card card-body mt-2">

<div class="d-flex justify-content-between align-items-center mb-3">

<h5 class="mb-0">Rejected</h5>

<form method="GET" class="d-flex align-items-center gap-2">

<input type="hidden" name="open" value="rejectedReq">

<label class="mb-0">Show</label>

<select name="limit"
class="form-select w-auto"
onchange="this.form.submit()">

<option value="5" <?php if($limit==5) echo 'selected'; ?>>5</option>
<option value="10" <?php if($limit==10) echo 'selected'; ?>>10</option>
<option value="20" <?php if($limit==20) echo 'selected'; ?>>20</option>

</select>

<span>entries</span>

</form>

</div>

<table class="table table-bordered table-hover">

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
$result = $conn->query("
SELECT * FROM requisitions 
WHERE status='Rejected'
AND deleted_by_principal = 0
ORDER BY id DESC 
LIMIT $rejectedStart, $limit
");

while($r = $result->fetch_assoc()):
?>

<tr>

<td><?php echo $r['requisition_number']; ?></td>
<td><?php echo $r['title']; ?></td>
<td><?php echo $r['department']; ?></td>
<td><?php echo $r['created_by']; ?></td>
<td><?php echo date("d M Y H:i", strtotime($r['created_at'])); ?></td>

<td>

<a href="view-requisition.php?id=<?php echo $r['id']; ?>&type=rejected&from=principal"
class="btn btn-sm btn-primary">
View
</a>

<a href="soft-delete.php?id=<?php echo $r['id']; ?>&role=principal"
class="btn btn-sm btn-danger"
onclick="return confirm('Are you sure you want to delete this requisition?')">
Delete
</a>

</td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

<nav class="d-flex justify-content-end">

<ul class="pagination">

<?php if($rejectedPage > 1): ?>

<li class="page-item">
<a class="page-link"
href="?rejected_page=<?php echo $rejectedPage-1; ?>&limit=<?php echo $limit; ?>&open=rejectedReq">
Previous
</a>
</li>

<?php endif; ?>

<?php for($i=1; $i <= $rejectedPages; $i++): ?>

<li class="page-item <?php if($i == $rejectedPage) echo 'active'; ?>">

<a class="page-link"
href="?rejected_page=<?php echo $i; ?>&limit=<?php echo $limit; ?>&open=rejectedReq">

<?php echo $i; ?>

</a>

</li>

<?php endfor; ?>

<?php if($rejectedPage < $rejectedPages): ?>

<li class="page-item">
<a class="page-link"
href="?rejected_page=<?php echo $rejectedPage+1; ?>&limit=<?php echo $limit; ?>&open=rejectedReq">
Next
</a>
</li>

<?php endif; ?>

</ul>

</nav>

</div>
</div>

</div>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>