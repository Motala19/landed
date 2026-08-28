<?php
session_start();
include 'includes/db.php';

// Must come from login first
if (!isset($_SESSION['pending_user_id'])) {
    header("Location: login.php");
    exit;
}

$error = '';
$email = $_SESSION['pending_email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = trim($_POST['otp'] ?? '');

    if ($otp === '') {
        $error = "Please enter the verification code";
    } else {
        $userId = (int)$_SESSION['pending_user_id'];

        $stmt = $conn->prepare("SELECT otp_code, otp_expires_at FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if (!$user || empty($user['otp_code'])) {
            $error = "No verification code found. Please login again.";
        } elseif (strtotime($user['otp_expires_at']) < time()) {
            $error = "Code expired. Please login again.";
        } elseif ($user['otp_code'] !== $otp) {
            $error = "Incorrect verification code";
        } else {
            // OTP correct - clear OTP and complete login
            $clear = $conn->prepare("UPDATE users SET otp_code = NULL, otp_expires_at = NULL WHERE id = ?");
            $clear->bind_param("i", $userId);
            $clear->execute();

            $_SESSION['user_id']   = $_SESSION['pending_user_id'];
            $_SESSION['full_name'] = $_SESSION['pending_name'];
            $_SESSION['role']      = $_SESSION['pending_role'];
            $_SESSION['email']     = $_SESSION['pending_email'];
            $_SESSION['last_activity'] = time();

            $mustChange = $_SESSION['pending_must_change'] ?? 0;

            // Clear pending session values
            unset($_SESSION['pending_user_id'], $_SESSION['pending_email'], $_SESSION['pending_name'], $_SESSION['pending_role'], $_SESSION['pending_must_change']);

            if ($mustChange == 1) {
                header("Location: change-password.php");
                exit;
            }

            switch ($_SESSION['role']) {
                case 'staff':
                    header("Location: requisitions.php");
                    break;
                case 'principal':
                    header("Location: principal-dashboard.php");
                    break;
                case 'finance':
                    header("Location: finance-dashboard.php");
                    break;
                case 'treasurer':
                    header("Location: treasurer-dashboard.php");
                    break;
                case 'admin':
                    header("Location: dashboard.php");
                    break;
                default:
                    header("Location: requisitions.php");
            }
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Login - Midrand Primary School</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #0B1D39; }
        .login-box {
            max-width: 420px;
            margin: 120px auto;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="login-box card">
        <div class="card-body p-5">

            <div class="text-center mb-4">
                <h4 class="fw-bold">Email Verification</h4>
                <p class="text-muted">
                    We sent a 6-digit code to<br>
                    <strong><?= htmlspecialchars($email) ?></strong>
                </p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger text-center">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-4">
                    <label class="form-label">Verification Code</label>
                    <input type="text" name="otp" class="form-control text-center"
                           maxlength="6" pattern="[0-9]{6}" inputmode="numeric"
                           placeholder="Enter 6-digit code" required autofocus>
                </div>

                <button type="submit" class="btn btn-danger w-100 py-3 fw-bold">VERIFY & LOGIN</button>
            </form>

            <div class="text-center mt-4">
                <a href="login.php" class="text-muted small">Back to Login</a>
            </div>

        </div>
    </div>
</div>

</body>
</html>