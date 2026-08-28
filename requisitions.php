<?php 
session_start();
date_default_timezone_set('Africa/Johannesburg');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!in_array($_SESSION['role'], ['finance', 'admin', 'staff', 'treasurer', 'principal'])) {
    header("Location: requisitions.php");
    exit;
}

$userName = $_SESSION['full_name'] ?? 'User';
$role = $_SESSION['role'];
$currentPage = basename($_SERVER['PHP_SELF']);



$userName = $_SESSION['full_name'] ?? 'User';
$role = $_SESSION['role'];

include 'includes/db.php';

// =============================
// PAGINATION
// =============================

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;

if (!in_array($limit, [5,10,20])) {
    $limit = 5;
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$start = ($page - 1) * $limit;

// =============================
// TOTAL RECORDS
// =============================

$countStmt = $conn->prepare("
SELECT COUNT(*) as total 
FROM requisitions
WHERE created_by = ?
AND deleted_at IS NULL
");

$countStmt->bind_param("s", $userName);
$countStmt->execute();

$totalResult = $countStmt->get_result();
$totalRows = $totalResult->fetch_assoc()['total'];

$totalPages = ceil($totalRows / $limit);

// =============================
// FETCH REQUISITIONS
// =============================

$stmt = $conn->prepare("
SELECT * FROM requisitions
WHERE created_by = ?
AND deleted_at IS NULL
ORDER BY created_at DESC
LIMIT ?, ?
");

$stmt->bind_param("sii", $userName, $start, $limit);
$stmt->execute();

$result = $stmt->get_result();

$currentDate = date("l, d F Y");
$currentTime = date("H:i A");

function badgeClass($status) {
    return match (strtolower($status)) {
        'approved' => 'bg-success-subtle text-success',
        'rejected' => 'bg-danger-subtle text-danger',
        'pending'  => 'bg-warning-subtle text-warning',
        'new'      => 'bg-primary-subtle text-primary',
        default    => 'bg-secondary-subtle text-secondary'
    };
}
?>

<!DOCTYPE html>
<html>

<head>

<title>My Requisitions</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="assets/css/style.css">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

</head>

<body>

<div class="container-fluid">
<div class="row">

<?php include 'includes/sidebar.php'; ?>

<div class="col-lg-10 p-4">

<!-- HEADER -->

<div class="d-flex justify-content-between align-items-center mb-4">

<div>

<br><br>

<h3>My Requisitions</h3>

<small>
<?php echo $currentDate . " | " . $currentTime; ?>
</small>

</div>

<div class="text-end">

<div>
<strong><?php echo htmlspecialchars($userName); ?></strong>
</div>

<small class="text-muted">
<?php echo ucfirst($role); ?>
</small>

<br>

<a href="create_requisition.php" class="btn btn-primary mt-3">
<i class="bi bi-plus-lg"></i> Create New Requisition
</a>

</div>

</div>

<!-- TABLE CARD -->

<div class="card">

<div class="card-body">

<!-- TOP BAR -->

<div class="d-flex justify-content-end align-items-center mb-3">

<form method="GET" class="d-flex align-items-center gap-2">

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

<!-- TABLE -->

<table class="table table-hover table-bordered">

<thead>

<tr>

<th>Number</th>
<th>Title</th>
<th>Department</th>
<th>Date</th>
<th>Status</th>
<th>Actions</th>

</tr>

</thead>

<tbody>

<?php if ($result->num_rows == 0): ?>

<tr>

<td colspan="6" class="text-center py-5 text-muted">
You have not created any requisitions yet.
</td>

</tr>

<?php else: ?>

<?php while($r = $result->fetch_assoc()): ?>

<tr>

<td><?php echo $r['requisition_number']; ?></td>

<td><?php echo htmlspecialchars($r['title']); ?></td>

<td><?php echo htmlspecialchars($r['department']); ?></td>

<td><?php echo date("d M Y H:i", strtotime($r['created_at'])); ?></td>

<td>

<span class="badge <?php echo badgeClass($r['status']); ?>">

<?php echo ucfirst($r['status']); ?>

</span>

</td>

<td>

<!-- Always show View -->
<a href="view-requisition.php?id=<?php echo $r['id']; ?>&from=requisitions"
   class="btn btn-sm btn-primary">
    View
</a>

<!-- Show Edit only if Rejected -->
<?php if($r['status'] == 'Rejected'): ?>
    <a href="edit-requisition.php?id=<?php echo $r['id']; ?>"
       class="btn btn-sm btn-warning">
        Edit
    </a>
<?php endif; ?>

<!-- Show POP if Paid -->
<?php if ($r['status'] == 'Paid' && !empty($r['payment_proof'])): ?>
    <a href="uploads/<?php echo $r['payment_proof']; ?>" 
       class="btn btn-sm btn-info" target="_blank">
        POP
    </a>
<?php endif; ?>

<!-- Always show Delete -->
<a href="staff-delete.php?id=<?php echo $r['id']; ?>"
   class="btn btn-sm btn-danger"
   onclick="return confirm('Are you sure you want to delete this requisition?')">
    Delete
</a>
<!-- Upload Invoice (only if Paid and no invoice yet) -->
<?php if ($r['status'] == 'Paid' && empty($r['invoice'])): ?>
    <a href="upload-invoice.php?id=<?php echo $r['id']; ?>"
       class="btn btn-sm btn-warning">
        Upload Invoice
    </a>
<?php endif; ?>

<!-- View Invoice (after uploaded) -->
<?php if ($r['status'] == 'Paid' && !empty($r['invoice'])): ?>
    <a href="uploads/<?php echo htmlspecialchars($r['invoice']); ?>"
       class="btn btn-sm btn-success" target="_blank">
        View Invoice
    </a>
<?php endif; ?>




</td>

</tr>

<?php endwhile; ?>

<?php endif; ?>

</tbody>

</table>

<!-- PAGINATION -->

<nav class="d-flex justify-content-end">

<ul class="pagination">

<?php if($page > 1): ?>

<li class="page-item">

<a class="page-link"
href="?page=<?php echo $page-1; ?>&limit=<?php echo $limit; ?>">

Previous

</a>

</li>

<?php endif; ?>

<?php for($i = 1; $i <= $totalPages; $i++): ?>

<li class="page-item <?php if($i == $page) echo 'active'; ?>">

<a class="page-link"
href="?page=<?php echo $i; ?>&limit=<?php echo $limit; ?>">

<?php echo $i; ?>

</a>

</li>

<?php endfor; ?>

<?php if($page < $totalPages): ?>

<li class="page-item">

<a class="page-link"
href="?page=<?php echo $page+1; ?>&limit=<?php echo $limit; ?>">

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