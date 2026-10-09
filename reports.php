<?php
session_start();
date_default_timezone_set('Africa/Johannesburg');
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// View rights: Finance, Principal, Treasurer, Admin
if (!in_array($_SESSION['role'], ['finance', 'admin', 'principal', 'treasurer'])) {
    header("Location: requisitions.php");
    exit;
}

$userName = $_SESSION['full_name'] ?? 'User';
$role = $_SESSION['role'] ?? '';
$currentPage = basename($_SERVER['PHP_SELF']);
$currentDate = date("l, d F Y");
$currentTime = date("H:i");
$currentYear = (int)date('Y');

// Totals
$totalRequisitions = $conn->query("SELECT COUNT(*) AS c FROM requisitions")->fetch_assoc()['c'];
$totalPaidCount = $conn->query("SELECT COUNT(*) AS c FROM requisitions WHERE status='Paid'")->fetch_assoc()['c'];
$totalPaidAmount = $conn->query("SELECT COALESCE(SUM(amount),0) AS s FROM requisitions WHERE status='Paid'")->fetch_assoc()['s'];
$totalBudgetAllocation = $conn->query("SELECT COALESCE(SUM(budget_amount),0) AS s FROM department_budgets WHERE budget_year = $currentYear")->fetch_assoc()['s'];
$totalRemaining = $totalBudgetAllocation - $totalPaidAmount;

// Department rows for current year budgets + all-time/current spending by department name
$deptSql = "
SELECT 
    b.department,
    b.budget_amount,
    b.budget_year,
    COUNT(r.id) AS total_requisitions,
    SUM(CASE WHEN r.status = 'Paid' THEN 1 ELSE 0 END) AS paid_count,
    COALESCE(SUM(CASE WHEN r.status = 'Paid' THEN r.amount ELSE 0 END), 0) AS amount_spent
FROM department_budgets b
LEFT JOIN requisitions r ON r.department = b.department
WHERE b.budget_year = $currentYear
GROUP BY b.department, b.budget_amount, b.budget_year
ORDER BY b.department ASC
";
$deptResult = $conn->query($deptSql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports / Budgets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .stat-card { border:none; border-radius:14px; box-shadow:0 6px 18px rgba(11,29,57,.08); height:100%; }
        .stat-label { font-size:.8rem; text-transform:uppercase; letter-spacing:.4px; color:#6c757d; }
        .stat-value { font-size:1.55rem; font-weight:700; color:#0B1D39; }
        .accent-navy { border-left:5px solid #0B1D39; }
        .accent-blue { border-left:5px solid #1F3A5F; }
        .accent-green { border-left:5px solid #198754; }
        .accent-red { border-left:5px solid #dc3545; }
        .accent-orange { border-left:5px solid #fd7e14; }

        .table-budgets { border-collapse: separate; border-spacing: 0; }
        .table-budgets thead th {
            background:#0B1D39 !important;
            color:#fff !important;
            font-weight:700;
            border:none;
            padding:14px 12px;
            white-space:nowrap;
        }
        .table-budgets tbody td { padding:12px; vertical-align:middle; background:#fff; }
        .table-budgets tbody tr:nth-child(even) td { background:#f8fafc; }
        .col-budget { color:#0B1D39; font-weight:700; }
        .col-spent { color:#dc3545; font-weight:700; }
        .col-remaining-ok { color:#198754; font-weight:700; }
        .col-remaining-over { color:#dc3545; font-weight:700; }
        .section-title { color:#0B1D39; font-weight:700; border-bottom:2px solid #e9ecef; padding-bottom:8px; margin-bottom:18px; }
    </style>
</head>
<body>
<div class="container-fluid">
<div class="row">
<?php include 'includes/sidebar.php'; ?>

<div class="col-lg-10 p-4">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="mb-1">Reports / Budgets</h3>
            <small class="text-muted"><?= $currentDate ?> | <?= $currentTime ?> | Year <?= $currentYear ?></small>
        </div>
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <a href="reports-processes.php" class="btn btn-outline-dark btn-sm">
                <i class="fa-solid fa-list-check me-1"></i> Processes
            </a>
            <a href="export-year-budget-report.php?year=<?= $currentYear ?>" class="btn btn-outline-success btn-sm" target="_blank">
                <i class="fa-solid fa-file-pdf me-1"></i> Year PDF Report
            </a>
            <?php if (in_array($role, ['finance', 'admin'])): ?>
            <a href="budget-allocate.php" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-coins me-1"></i> Allocate Budgets
            </a>
           <?php if ($role === 'admin'): ?>
<a href="budget-year-reset.php"
   class="btn btn-outline-danger btn-sm"
   onclick="return confirm('ADMIN ONLY: Archive current budgets and set all allocations to R0.00? Departments will remain. Finance can allocate again after this.');">
   Year-End Reset
</a>
<?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <h5 class="section-title">Overview</h5>
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card accent-navy p-3">
                <div class="stat-label">Total Requisitions</div>
                <div class="stat-value"><?= (int)$totalRequisitions ?></div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card accent-blue p-3">
                <div class="stat-label">Total Budget Allocation</div>
                <div class="stat-value">R<?= number_format((float)$totalBudgetAllocation, 2) ?></div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card accent-red p-3">
                <div class="stat-label">Total Spent (Paid)</div>
                <div class="stat-value" style="color:#dc3545;">R<?= number_format((float)$totalPaidAmount, 2) ?></div>
                <small class="text-muted"><?= (int)$totalPaidCount ?> paid</small>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card accent-green p-3">
                <div class="stat-label">Total Remaining</div>
                <div class="stat-value <?= $totalRemaining < 0 ? 'col-remaining-over' : 'col-remaining-ok' ?>">
                    R<?= number_format((float)$totalRemaining, 2) ?>
                </div>
            </div>
        </div>
    </div>

    <h5 class="section-title">Department Budgets & Spending</h5>
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-budgets mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Department</th>
                        <th class="text-center">Total Requisitions</th>
                        <th class="text-center">Paid Requisitions</th>
                        <th class="text-end">Initial Budget</th>
                        <th class="text-end">Amount Spent</th>
                        <th class="text-end">Remaining</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($deptResult && $deptResult->num_rows > 0): ?>
                    <?php while ($row = $deptResult->fetch_assoc()):
                        $budget = (float)$row['budget_amount'];
                        $spent = (float)$row['amount_spent'];
                        $remaining = $budget - $spent;
                    ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($row['department']) ?></strong></td>
                        <td class="text-center"><?= (int)$row['total_requisitions'] ?></td>
                        <td class="text-center"><?= (int)$row['paid_count'] ?></td>
                        <td class="text-end col-budget">R<?= number_format($budget, 2) ?></td>
                        <td class="text-end col-spent">R<?= number_format($spent, 2) ?></td>
                        <td class="text-end <?= $remaining < 0 ? 'col-remaining-over' : 'col-remaining-ok' ?>">
                            R<?= number_format($remaining, 2) ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No budget data for <?= $currentYear ?> yet.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</div>
</div>
</body>
</html>