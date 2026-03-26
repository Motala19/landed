<?php 
session_start();
$userName = "Finance User";

$quotes = [
    [
        'document' => 'Sports Equipment Quote 1.pdf',
        'requisition' => 'REQ-001',
        'created_by' => 'Motala Godfrey',
        'date' => '2026-03-20',
        'status' => 'Pending Finance'
    ],
    [
        'document' => 'Sports Equipment Quote 2.pdf',
        'requisition' => 'REQ-001',
        'created_by' => 'Motala Godfrey',
        'date' => '2026-03-20',
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
    <title>Finance Quotes</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
<div class="row">

<?php include 'includes/sidebar.php'; ?>

<div class="col-lg-10 p-4">
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Quotes</h3>
    <div class="text-end">
        <strong><?php echo $userName; ?></strong>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Document</th>
                <th>Requisition</th>
                <th>Created By</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $i=1; foreach($quotes as $q): ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo $q['document']; ?></td>
                <td><?php echo $q['requisition']; ?></td>
                <td><?php echo $q['created_by']; ?></td>
                <td><?php echo $q['date']; ?></td>
                <td>
                    <span class="badge <?php echo badgeClass($q['status']); ?>">
                        <?php echo $q['status']; ?>
                    </span>
                </td>
                <td>
                    <a href="finance-verification.php?type=quote&id=<?php echo $i; ?>" class="btn btn-sm btn-primary">View</a>
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
</div>
</body>
</html>