<?php
session_start();
include 'includes/db.php';

$error = '';

// Get saved email from cookie (if exists)
$savedEmail = $_COOKIE['remember_email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    if (empty($email) || empty($password)) {
        $error = "Please enter email and password";
    } else {
        $stmt = $conn->prepare("SELECT id, full_name, email, role, password, must_change_password, failed_attempts, lock_until 
                                FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Check if account is currently locked
            if (!empty($user['lock_until']) && strtotime($user['lock_until']) > time()) {
                $remaining = ceil((strtotime($user['lock_until']) - time()) / 60);
                $error = "Account is locked. Try again in $remaining minute(s).";
            } else {
                // Check password
                if (password_verify($password, $user['password'])) {

                    // Reset failed attempts on successful login
                    $reset = $conn->prepare("UPDATE users SET failed_attempts = 0, lock_until = NULL WHERE id = ?");
                    $reset->bind_param("i", $user['id']);
                    $reset->execute();

                    // Remember email if checkbox is ticked
                    if ($remember) {
                        setcookie('remember_email', $email, time() + (180 * 24 * 60 * 60), "/"); // 6 months
                    } else {
                        setcookie('remember_email', '', time() - 3600, "/"); // delete cookie
                    }

                    // Save user info in session
                    $_SESSION['user_id']   = $user['id'];
                    $_SESSION['full_name'] = $user['full_name'];
                    $_SESSION['role']      = $user['role'];
                    $_SESSION['email']     = $user['email'];

                    // If user must change password (new account)
                    if ($user['must_change_password'] == 1) {
                        header("Location: change-password.php");
                        exit;
                    }

                    // Role-based redirect
                    switch ($user['role']) {
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

                } else {
                    // Wrong password - increase failed attempts
                    $attempts = $user['failed_attempts'] + 1;

                    if ($attempts >= 5) {
                        // Lock account for 10 minutes
                        $lockUntil = date('Y-m-d H:i:s', strtotime('+10 minutes'));
                        $update = $conn->prepare("UPDATE users SET failed_attempts = ?, lock_until = ? WHERE id = ?");
                        $update->bind_param("isi", $attempts, $lockUntil, $user['id']);
                        $update->execute();
                        $error = "Too many failed attempts. Account locked for 10 minutes.";
                    } else {
                        $update = $conn->prepare("UPDATE users SET failed_attempts = ? WHERE id = ?");
                        $update->bind_param("ii", $attempts, $user['id']);
                        $update->execute();
                        $remaining = 5 - $attempts;
                        $error = "Incorrect email or password. $remaining attempt(s) remaining.";
                    }
                }
            }
        } else {
            $error = "Email not found";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Midrand Primary School</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #0B1D39;
        }
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
                <img src="assets/images/logo.png" 
                     alt="Midrand Primary School Logo" 
                     class="mx-auto mb-3" 
                     style="width: 110px; height: 110px; object-fit: contain;">
                
                <h4 class="fw-bold">Midrand Primary School</h4>
                <p class="text-muted">Requisition Management</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger text-center">
                    <?= htmlspecialchars($error) ?> 
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" 
                           value="<?= htmlspecialchars($savedEmail) ?>" 
                           required autofocus>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="mb-4 form-check">
                    <input type="checkbox" name="remember" class="form-check-input" id="remember" 
                           <?= $savedEmail ? 'checked' : '' ?>>
                    <label class="form-check-label" for="remember">
                        Remember me
                    </label>
                </div>

                <button type="submit" class="btn btn-danger w-100 py-3 fw-bold">LOGIN</button>
            </form>

            <div class="text-center mt-4">
                <small class="text-muted">Forgot password? Contact Finance or Admin</small>
            </div>

        </div>
    </div>
</div>

</body>
</html>