<?php 
include 'includes/db.php';

$result = $conn->query("SELECT * FROM requisitions ORDER BY created_at DESC");
session_start();

$userName = "Motala Godfrey";
$currentDate = date("l, d F Y");
$currentTime = date("H:i:s");

$requisitions = [
    [
        'number' => 'REQ-001',
        'title' => 'Sports Equipment',
        'department' => 'Sports',
        'status' => 'Pending Principal',
        'date' => '2026-03-20',
        'created_by' => 'Motala Godfrey'
    ],
    [
        'number' => 'REQ-002',
        'title' => 'Math Textbooks',
        'department' => 'Academics',
        'status' => 'Approved',
        'date' => '2026-03-18',
        'created_by' => 'Motala Godfrey'
    ],
    [
        'number' => 'REQ-003',
        'title' => 'Football Kit',
        'department' => 'Sports',
        'status' => 'Rejected',
        'date' => '2026-03-19',
        'created_by' => 'Motala Godfrey'
    ]
];

function badgeClass($status) {
    return match ($status) {
        'Approved' => 'bg-success-subtle text-success',
        'Rejected' => 'bg-danger-subtle text-danger',
        'Pending Principal' => 'bg-warning-subtle text-warning',
        default => 'bg-secondary'
    };
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Requisitions</title>
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
        <h3>My Requisitions</h3>
        <small><?php echo $currentDate . " | " . $currentTime; ?></small>
    </div>

    <!-- USER INFO + BUTTON -->
    <div class="text-end">
        <div><strong><?php echo $userName; ?></strong></div>
        <small class="text-muted">Staff</small>
        <br><br>
        <a href="create_requisition.php">
        <button class="btn btn-primary mt-2">
            + Create Requisition
        </button>
        </a>
    </div>

</div>

<!-- SEARCH / FILTER -->
<div class="card-box mb-3">
    <input style="background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 10px;" type="text" class="form-control" placeholder="Search requisitions...">
</div>

<!-- TABLE -->
<div class="card-box">
    <table class="table">
        <thead>
            <tr>
                <th>Number</th>
                <th>Title</th>
                <th>Department</th>
                <th>Date</th>
                <th>Created By</th> <!-- ✅ NEW -->
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            <?php while($r = $result->fetch_assoc()): ?>
<tr>
    <td>
    <?php echo $r['requisition_number']; ?>
    </td>
    <td><?php echo $r['title']; ?></td>
    <td><?php echo $r['department']; ?></td>
    <td><?php echo $r['created_at']; ?></td>
    <td><?php echo $r['created_by']; ?></td>
   <td>
    <span class="badge <?php echo badgeClass($r['status']); ?>">
        <?php echo $r['status']; ?>
    </span>

    <?php if($r['status'] == 'Rejected' && !empty($r['rejection_reason'])): ?>
        <div class="text-danger small mt-1">
            Reason: <?php echo $r['rejection_reason']; ?>
        </div>
    <?php endif; ?>
</td>
    

    <td>
    <a href="edit-requisition.php?id=<?php echo $r['id']; ?>" class="btn btn-sm btn-edit">
        Edit
    </a>

    <a href="delete-requisition.php?id=<?php echo $r['id']; ?>" 
       class="btn btn-sm btn-danger"
       onclick="return confirm('Are you sure?')">
        Delete
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

<style>
.btn-edit {
    background: #1F3A5F;
    color: white;
    border: none;
    margin-right: 5px;
}
.btn-edit:hover {
    background: #7A1F2B;
}
</style>

</body>
</html>