<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($new_password) || empty($confirm_password)) {
        $error = "Please fill in both password fields";
    } elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($new_password) < 6) {
        $error = "Password must be at least 6 characters";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $user_id = $_SESSION['user_id'];

        $stmt = $conn->prepare("UPDATE users SET password = ?, must_change_password = 0 WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $user_id);
        $stmt->execute();

        $success = "Password changed successfully!";

        // Redirect after success
        $role = $_SESSION['role'];
        switch ($role) {
            case 'staff': header("Location: requisitions.php"); break;
            case 'principal': header("Location: principal-dashboard.php"); break;
            case 'finance': header("Location: finance-dashboard.php"); break;
            case 'treasurer': header("Location: treasurer-dashboard.php"); break;
            case 'admin': header("Location: dashboard.php"); break;
            default: header("Location: requisitions.php");
        }
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background: #f8f9fa; }
        .change-box { max-width: 450px; margin: 120px auto; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="container">
    <div class="change-box card">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <img src="assets/images/logo.png" alt="Logo" style="width: 90px; height: 90px; object-fit: contain;">
                <h4 class="fw-bold mt-3">Set New Password</h4>
                <p class="text-muted">This is your first login. Please create a new password.</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label>New Password</label>
                    <input type="password" name="new_password" class="form-control" required>
                </div>
                <div class="mb-4">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-danger w-100 py-3">CHANGE PASSWORD</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>