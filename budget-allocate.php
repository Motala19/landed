<?php
session_start();
date_default_timezone_set('Africa/Johannesburg');
include 'includes/db.php';
include 'includes/audit_logger.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!in_array($_SESSION['role'], ['finance', 'admin'])) {
    header("Location: reports.php");
    exit;
}

$userName = $_SESSION['full_name'] ?? 'User';
$role = $_SESSION['role'] ?? '';
$userId = (int)$_SESSION['user_id'];
$currentYear = (int)date('Y');
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_budgets'])) {
    $departments = $_POST['department'] ?? [];
    $amounts = $_POST['budget_amount'] ?? [];

    $selectOld = $conn->prepare("SELECT id, budget_amount FROM department_budgets WHERE department = ? AND budget_year = ? LIMIT 1");
    $updateStmt = $conn->prepare("UPDATE department_budgets SET budget_amount = ?, updated_by = ? WHERE id = ?");
    $insertStmt = $conn->prepare("INSERT INTO department_budgets (department, budget_amount, budget_year, updated_by) VALUES (?, ?, ?, ?)");

    $updated = 0;

    foreach ($departments as $i => $dept) {
        $dept = trim($dept);
        $amount = (float)($amounts[$i] ?? 0);
        if ($dept === '') continue;

        $selectOld->bind_param("si", $dept, $currentYear);
        $selectOld->execute();
        $existing = $selectOld->get_result()->fetch_assoc();

        if ($existing) {
            $oldAmount = (float)$existing['budget_amount'];
            $id = (int)$existing['id'];

            $updateStmt->bind_param("dsi", $amount, $userName, $id);
            if ($updateStmt->execute()) {
                $updated++;
                log_audit(
                    $userId,
                    $userName,
                    $role,
                    'Budget Updated',
                    0,
                    "Department: $dept | Year: $currentYear | Old: R" . number_format($oldAmount, 2) . " | New: R" . number_format($amount, 2)
                );
            } else {
                $error = "Update failed for $dept: " . $updateStmt->error;
            }
        } else {
            $insertStmt->bind_param("sdis", $dept, $amount, $currentYear, $userName);
            if ($insertStmt->execute()) {
                $updated++;
                log_audit(
                    $userId,
                    $userName,
                    $role,
                    'Budget Created',
                    0,
                    "Department: $dept | Year: $currentYear | Amount: R" . number_format($amount, 2)
                );
            } else {
                $error = "Insert failed for $dept: " . $insertStmt->error;
            }
        }
    }

    if ($updated > 0 && $error === '') {
        $success = "Budgets saved successfully ($updated department(s)).";
    } elseif ($updated === 0 && $error === '') {
        $error = "No departments were saved. Check that department rows exist for $currentYear.";
    }
}

$budgetRows = $conn->query("
    SELECT department, budget_amount
    FROM department_budgets
    WHERE budget_year = $currentYear
    ORDER BY department ASC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Allocate Budgets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light">
<div class="container mt-4 mb-5" style="max-width:800px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Allocate Department Budgets (<?= $currentYear ?>)</h3>
        <a href="reports.php" class="btn btn-secondary btn-sm">← Back to Reports</a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <p class="text-muted">Only Finance/Admin can change allocations. Every save is written to Processes.</p>

            <?php if (!$budgetRows || $budgetRows->num_rows === 0): ?>
                <div class="alert alert-warning">
                    No departments found for year <?= $currentYear ?>.
                    Run the SQL insert for department_budgets first.
                </div>
            <?php else: ?>
            <form method="POST">
                <table class="table align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Department</th>
                            <th style="width:220px">Budget (R)</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($b = $budgetRows->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <input type="hidden" name="department[]" value="<?= htmlspecialchars($b['department']) ?>">
                                <strong><?= htmlspecialchars($b['department']) ?></strong>
                            </td>
                            <td>
                                <input type="number" step="0.01" min="0" class="form-control"
                                       name="budget_amount[]"
                                       value="<?= htmlspecialchars($b['budget_amount']) ?>" required>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
                <div class="text-end">
                    <button type="submit" name="save_budgets" value="1" class="btn btn-primary">Save Budgets</button>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>