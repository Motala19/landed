<?php
session_start();
date_default_timezone_set('Africa/Johannesburg');
include 'includes/db.php';
include 'includes/audit_logger.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// ADMIN ONLY
if (($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: reports.php");
    exit;
}

$userName = $_SESSION['full_name'] ?? 'User';
$role = $_SESSION['role'];
$userId = $_SESSION['user_id'];
$year = (int)date('Y');

// Snapshot current budgets + spending into archive
$q = $conn->query("
    SELECT b.department, b.budget_amount, b.budget_year,
        COUNT(r.id) AS total_requisitions,
        SUM(CASE WHEN r.status='Paid' THEN 1 ELSE 0 END) AS paid_count,
        COALESCE(SUM(CASE WHEN r.status='Paid' THEN r.amount ELSE 0 END),0) AS amount_spent
    FROM department_budgets b
    LEFT JOIN requisitions r 
        ON r.department = b.department 
        AND YEAR(r.created_at) = b.budget_year
    WHERE b.budget_year = $year
    GROUP BY b.department, b.budget_amount, b.budget_year
");

$ins = $conn->prepare("
    INSERT INTO department_budget_history
    (budget_year, department, budget_amount, amount_spent, remaining, total_requisitions, paid_count, archived_by)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

if ($q) {
    while ($row = $q->fetch_assoc()) {
        $budget = (float)$row['budget_amount'];
        $spent = (float)$row['amount_spent'];
        $remaining = $budget - $spent;
        $dept = $row['department'];
        $y = (int)$row['budget_year'];
        $totalReq = (int)$row['total_requisitions'];
        $paidCount = (int)$row['paid_count'];

        $ins->bind_param("isddiiis", $y, $dept, $budget, $spent, $remaining, $totalReq, $paidCount, $userName);
        $ins->execute();
    }
}

// Reset amounts to 0 — keep departments and same year
$conn->query("
    UPDATE department_budgets
    SET budget_amount = 0,
        updated_by = '" . $conn->real_escape_string($userName) . "'
    WHERE budget_year = $year
");

log_audit(
    $userId,
    $userName,
    $role,
    'Budget Year Reset',
    null,
    "Archived $year budgets and reset all department allocations to R0.00"
);

header("Location: reports.php?success=year_reset");
exit;