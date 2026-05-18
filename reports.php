<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Only Finance and Admin can see full reports for now
if (!in_array($_SESSION['role'], ['finance', 'admin', 'principal', 'treasurer'])) {
    header("Location: requisitions.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports - Midrand Primary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>

        <div class="col-lg-10 p-4">
            <h2>Audit Reports & Activity Log</h2>
            <p class="text-muted">Track all actions in the requisition system</p>

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
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php if ($result->num_rows == 0): ?>
                <div class="alert alert-info">No activity logged yet.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>