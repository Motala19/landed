<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!in_array($_SESSION['role'], ['finance', 'admin'])) {
    header("Location: requisitions.php");
    exit;
}

$userName = $_SESSION['full_name'] ?? 'User';
$role = $_SESSION['role'] ?? '';
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Processes - Midrand Primary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container-fluid">
<div class="row">
<?php include 'includes/sidebar.php'; ?>

<div class="col-lg-10 p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2>Processes / Activity Log</h2>
            <p class="text-muted mb-0">Track all actions in the requisition system</p>
        </div>
        <a href="reports.php" class="btn btn-outline-primary">← Back to Reports / Budgets</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Date & Time</th>
                        <th>User</th>
                        <th>Role</th>
                        <th>Action</th>
                        <th>Requisition ID</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $result = $conn->query("SELECT * FROM audit_logs ORDER BY created_at DESC LIMIT 200");
                if ($result && $result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                ?>
                    <tr>
                        <td><?= date("d M Y H:i", strtotime($row['created_at'])) ?></td>
                        <td><?= htmlspecialchars($row['user_name']) ?></td>
                        <td><span class="badge bg-secondary"><?= strtoupper($row['role']) ?></span></td>
                        <td><?= htmlspecialchars($row['action']) ?></td>
                        <td><?= $row['requisition_id'] ? '#' . $row['requisition_id'] : '-' ?></td>
                        <td><?= htmlspecialchars($row['details'] ?? '') ?></td>
                    </tr>
                <?php
                    endwhile;
                else:
                ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted">No activity logged yet.</td>
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