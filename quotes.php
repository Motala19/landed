<?php 
session_start();

$userName = "Motala Godfrey";
$currentDate = date("l, d F Y");
$currentTime = date("H:i:s");

$quotes = [
    [
        'document' => 'Sports Equipment Quote 1.pdf',
        'status' => 'pending',
        'date' => '2026-03-20',
        'created_by' => 'Motala Godfrey'
    ],
    [
        'document' => 'Sports Equipment Quote 2.pdf',
        'status' => 'Rejected',
        'date' => '2026-03-20',
        'created_by' => 'Jane Smith'
    ]
];

function badgeClass($status) {
    return match ($status) {
        'Approved' => 'bg-success-subtle text-success',
        'Rejected' => 'bg-danger-subtle text-danger',
        'pending' => 'bg-warning-subtle text-warning',
        default => 'bg-secondary'
    };
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Staff Quotes</title>
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
        <h3>My Quotes</h3>
        <small><?php echo $currentDate . " | " . $currentTime; ?></small>
    </div>

    <!-- USER INFO + BUTTON -->
    <div class="text-end">
        <div><strong><?php echo $userName; ?></strong></div>
        <small class="text-muted">Staff</small>
        <br>
        <button class="btn btn-primary mt-2">+ Upload Quote</button>
    </div>

</div>

<!-- TABLE -->
<div class="card-box">
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Document Name</th>
                <th>Date Uploaded</th>
                <th>Created By</th> <!-- ✅ NEW -->
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            <?php $i = 1; foreach($quotes as $q): ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo $q['document']; ?></td>
                <td><?php echo $q['date']; ?></td>
                <td><?php echo $q['created_by']; ?></td> <!-- ✅ NEW -->
                <td>
                    <span class="badge <?php echo badgeClass($q['status']); ?>">
                        <?php echo $q['status']; ?>
                    </span>
                </td>
                <td>
                    <?php if($q['status'] == 'pending' || $q['status'] == 'Rejected'): ?>
                        <button class="btn btn-sm btn-edit">Re-upload</button>
                        <button class="btn btn-sm btn-edits">Delete</button>
                    <?php else: ?>
                        <span class="text-muted">Locked</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
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
}
.btn-edit:hover {
    background: #7A1F2B;
}
</style>

</body>
</html>