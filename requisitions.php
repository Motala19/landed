<?php 
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Use the correct session variable
$userName = $_SESSION['full_name'] ?? 'User';
$role = $_SESSION['role'];

include 'includes/db.php';

// FIXED: Show ONLY the logged-in user's own requisitions
$stmt = $conn->prepare("
    SELECT * FROM requisitions 
    WHERE created_by = ? 
      AND deleted_at IS NULL 
    ORDER BY created_at DESC
");
$stmt->bind_param("s", $userName);
$stmt->execute();
$result = $stmt->get_result();

$currentDate = date("l, d F Y");
$currentTime = date("H:i:s");

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
       <br><br> <h3>My Requisitions</h3>
        <small><?php echo $currentDate . " | " . $currentTime; ?></small>
    </div>

    <div class="text-end">
        <div><strong><?php echo htmlspecialchars($userName); ?></strong></div>
        <small class="text-muted"><?= ucfirst($role) ?></small><br>
        
        <a href="create_requisition.php" class="btn btn-primary mt-3">
            <i class="bi bi-plus-lg"></i> Create New Requisition
        </a>
        
    </div>
</div>

<!-- TABLE -->
<div class="card">
    <div class="card-body">
        <table class="table table-hover">
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
                    <td><?php echo date("d M Y", strtotime($r['created_at'])); ?></td>
                    
                    <td>
                        <span class="badge <?php echo badgeClass($r['status']); ?>">
                            <?php echo ucfirst($r['status']); ?>
                        </span>
                    </td>

                    <td>
                        <?php if($r['status'] != 'Rejected'): ?>
                            <a href="view-requisition.php?id=<?php echo $r['id']; ?>" 
                               class="btn btn-sm btn-primary">View</a>
                        <?php endif; ?>

                        <?php if($r['status'] == 'Rejected'): ?>
                            <a href="edit-requisition.php?id=<?php echo $r['id']; ?>" 
                               class="btn btn-sm btn-warning">Edit</a>
                        <?php endif; ?>

                        <a href="staff-delete.php?id=<?php echo $r['id']; ?>" 
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Remove from your view?')">
                            Delete
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>