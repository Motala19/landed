<?php 
session_start();
$userName = "Finance User";

$requisitions = [
    [
        'number' => 'REQ-001',
        'title' => 'Sports Equipment',
        'department' => 'Sports',
        'amount' => 5000,
        'budget' => 'Yes',
        'created_by' => 'Motala Godfrey',
        'date' => '2026-03-20',
        'status' => 'Pending Finance'
    ],
    [
        'number' => 'REQ-002',
        'title' => 'Math Textbooks',
        'department' => 'Academics',
        'amount' => 3500,
        'budget' => 'Yes',
        'created_by' => 'John Doe',
        'date' => '2026-03-18',
        'status' => 'Pending Finance'
    ]
];

function badgeClass($status) {
    return match ($status) {
        'Verified' => 'bg-success-subtle text-success',
        'Rejected' => 'bg-danger-subtle text-danger',
        'Pending Finance' => 'bg-warning-subtle text-warning',
        default => 'bg-secondary'
    };
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Finance Requisitions</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    
<div class="container-fluid">
    
<div class="row">

<?php include 'includes/sidebar.php'; ?>


<div class="col-lg-10 p-4">
    
<div class="d-flex justify-content-between align-items-center mb-4">
    
    <h3>Requisitions</h3>
    <div class="text-end">
        <strong><?php echo $userName; ?></strong>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Number</th>
                <th>Title</th>
                <th>Department</th>
                <th>Amount (R)</th>
                <th>Budget</th>
                <th>Created By</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($requisitions as $r): ?>
            <tr>
                <td><?php echo $r['number']; ?></td>
                <td><?php echo $r['title']; ?></td>
                <td><?php echo $r['department']; ?></td>
                <td><?php echo $r['amount']; ?></td>
                <td><?php echo $r['budget']; ?></td>
                <td><?php echo $r['created_by']; ?></td>
                <td><?php echo $r['date']; ?></td>
                <td>
                    <span class="badge <?php echo badgeClass($r['status']); ?>">
                        <?php echo $r['status']; ?>
                    </span>
                </td>
                <td>
                    <a href="finance-verification.php?type=requisition&id=<?php echo $r['number']; ?>" class="btn btn-sm btn-primary">View</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="finance-dashboard.php" class="btn btn-secondary mb-2">← Back</a>
           
        </div>
</div>
</div>
</div>
<div>
            
</div>

</body>
</html>