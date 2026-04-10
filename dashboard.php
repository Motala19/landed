<?php
session_start();

// USER INFO
$userName = "Motala Godfrey";
$userRole = "admin";
$currentDate = date("l, d F Y");
$currentTime = date("H:i:s");

// =====================
// REQUISITIONS DATA
// =====================
$totalRequisitions = 2;
$pendingRequisitions = 1;
$approvedRequisitions = 1;
$rejectedRequisitions = 0;

$recentRequisitions = [
    [
        'number' => 'REQ-2026-0001',
        'title' => 'Sports Equipment Purchase',
        'department' => 'Sports',
        'date' => '2026-03-16 10:25',
        'status' => 'Pending Principal'
    ],
    [
        'number' => 'REQ-2026-0002',
        'title' => 'Maths Textbooks',
        'department' => 'Academics',
        'date' => '2026-03-15 09:10',
        'status' => 'Approved'
    ]
];

// =====================
// QUOTATIONS DATA
// =====================
$totalQuotes = 2;
$pendingQuotes = 1;
$approvedQuotes = 1;
$rejectedQuotes = 0;

$recentQuotes = [
    [
        'quote_number' => 'QTE-2026-0001',
        'title' => 'Soccer Kits',
        'department' => 'Sports',
        'date' => '2026-03-16 12:00',
        'status' => 'Pending Principal'
    ],
    [
        'quote_number' => 'QTE-2026-0002',
        'title' => 'Science Equipment',
        'department' => 'Academics',
        'date' => '2026-03-15 11:00',
        'status' => 'Approved'
    ]
];

// STATUS BADGE
function badgeClass($status) {
    return match ($status) {
        'Approved' => 'bg-success-subtle text-success',
        'Rejected' => 'bg-danger-subtle text-danger',
        'Pending Principal' => 'bg-warning-subtle text-warning-emphasis',
        default => 'bg-secondary-subtle text-secondary'
    };
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requisition System</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css"> <!-- YOUR CSS FILE -->
</head>

<body>

<div class="container-fluid">
<div class="row">

<!-- SIDEBAR -->
<div class="col-lg-2 p-0">
    <aside class="sidebar">
        <div class="brand-box text-center">
    <img src="assets/images/logo.png" alt="School Logo" class="brand-logo">

<<<<<<< HEAD
    

=======
>>>>>>> 7ab4caea6570dc3165596da215d94ef01da59283
    <div class="brand-title mt-2">Midrand Primary</div>
    <div class="brand-subtitle">Requisition System</div><br>
</div>

        <nav class="nav flex-column">
            <a href="#" class="nav-link active">Dashboard</a>
            <a href="requisitions.php" class="nav-link">Requisitions</a>
            <a href="quotes.php" class="nav-link">Quotes</a>
            <a href="#" class="nav-link">Finance</a>
<<<<<<< HEAD
            <a href="#" class="nav-link">Logout</a>
=======
>>>>>>> 7ab4caea6570dc3165596da215d94ef01da59283
        </nav>
    </aside>
</div>

<!-- MAIN CONTENT -->
<div class="col-lg-10 p-0">

<header class="topbar d-flex justify-content-between align-items-center">
    <div>
        <div class="page-title">Dashboard</div>
        <div class="page-subtitle">Overview</div>
    </div>

    <div class="text-end">
        <strong><?php echo $userName; ?></strong><br>
        <small><?php echo $currentDate . " | " . $currentTime; ?></small><br>
        <span class="role-badge"><?php echo $userRole; ?></span>
    </div>
</header>

<main class="content-area">

<!-- ===================== -->
<!-- REQUISITIONS -->
<!-- ===================== -->
<h6 class="text-muted">Your Requisitions Overview</h6>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card-box stat-card stat-accent-blue">
            <div>Total</div>
            <div class="stat-value"><?php echo $totalRequisitions; ?></div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-box stat-card stat-accent-maroon">
            <div>Pending</div>
            <div class="stat-value"><?php echo $pendingRequisitions; ?></div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-box stat-card stat-accent-green">
            <div>Approved</div>
            <div class="stat-value"><?php echo $approvedRequisitions; ?></div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-box stat-card stat-accent-red">
            <div>Rejected</div>
            <div class="stat-value"><?php echo $rejectedRequisitions; ?></div>
        </div>
    </div>
</div>

<!-- REQUISITIONS TABLE -->
<div class="card-box">
    <div class="section-head">
        <h5>Latest Requisitions</h5>
    </div>

    <div class="section-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Number</th>
                    <th>Title</th>
                    <th>Department</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($recentRequisitions as $r): ?>
                <tr>
                    <td><?php echo $r['number']; ?></td>
                    <td><?php echo $r['title']; ?></td>
                    <td><?php echo $r['department']; ?></td>
                    <td><?php echo $r['date']; ?></td>
                    <td><span class="badge <?php echo badgeClass($r['status']); ?>"><?php echo $r['status']; ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ===================== -->
<!-- QUOTATIONS -->
<!-- ===================== -->
<h6 class="text-muted mt-4">Your Quotations Overview</h6>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card-box stat-card stat-accent-purple">
            <div>Total</div>
            <div class="stat-value"><?php echo $totalQuotes; ?></div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-box stat-card stat-accent-orange">
            <div>Pending</div>
            <div class="stat-value"><?php echo $pendingQuotes; ?></div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-box stat-card stat-accent-teal">
            <div>Approved</div>
            <div class="stat-value"><?php echo $approvedQuotes; ?></div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-box stat-card stat-accent-red">
            <div>Rejected</div>
            <div class="stat-value"><?php echo $rejectedQuotes; ?></div>
        </div>
    </div>
</div>

<!-- QUOTES TABLE -->
<div class="card-box">
    <div class="section-head">
        <h5>Latest Quotations</h5>
    </div>

    <div class="section-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Quote #</th>
                    <th>Title</th>
                    <th>Department</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($recentQuotes as $q): ?>
                <tr>
                    <td><?php echo $q['quote_number']; ?></td>
                    <td><?php echo $q['title']; ?></td>
                    <td><?php echo $q['department']; ?></td>
                    <td><?php echo $q['date']; ?></td>
                    <td><span class="badge <?php echo badgeClass($q['status']); ?>"><?php echo $q['status']; ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</main>
</div>

</div>
</div>

<script src="assets/js/script.js"></script>
</body>
</html>