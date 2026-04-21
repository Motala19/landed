<?php




// Handle Add New User

session_start();
include 'includes/db.php';

// Make sure user is logged in the proper way.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Only Finance and Admin can access this page
if (!in_array($_SESSION['role'], ['finance', 'admin'])) {
    header("Location: requisitions.php"); // or dashboard
    exit;
}

$userName = $_SESSION['full_name'];
$role = $_SESSION['role'];

$error = '';
$success = '';

$error = '';
$success = '';

// Handle Add New User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $user_role = $_POST['role'] ?? '';

    if (empty($full_name) || empty($email) || empty($user_role)) {
        $error = "Please fill all fields";
    } else {
        $temp_password = "Temp" . rand(1000, 9999);
        $hashed = password_hash($temp_password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (full_name, email, role, password, must_change_password, created_by) 
                                VALUES (?, ?, ?, ?, 1, ?)");
        $stmt->bind_param("ssssi", $full_name, $email, $user_role, $hashed, $_SESSION['user_id']);
        
        if ($stmt->execute()) {
            $success = "User created successfully!<br>
                        <strong>Temporary Password:</strong> <code>$temp_password</code><br>
                        Please give this password to the user.";
            
            // Clear the form fields after successful submission
            $full_name = '';
            $email = '';
            $user_role = '';
        } else {
            $error = "Failed to create user. This email may already exist.";
        }
    }
}

// Display messages from session (for delete and reset password)
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users - Midrand Primary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>

        <div class="col-lg-10 p-4">
            <h2>Manage Users</h2>
            <p class="text-muted">Add and manage system users</p>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>

            <?php endif; ?>


            
                
            <!-- Add New User Form -->
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <h5>Add New User</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="full_name" class="form-control" 
                                       value="<?= htmlspecialchars($full_name ?? '') ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" 
                                       value="<?= htmlspecialchars($email ?? '') ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Role</label>
                                <select name="role" class="form-control" required>
                                    <option value="">Select Role</option>
                                    <option value="staff" <?= ($user_role ?? '') == 'staff' ? 'selected' : '' ?>>Staff</option>
                                    <option value="principal" <?= ($user_role ?? '') == 'principal' ? 'selected' : '' ?>>Principal</option>
                                    <option value="finance" <?= ($user_role ?? '') == 'finance' ? 'selected' : '' ?>>Finance</option>
                                    <option value="treasurer" <?= ($user_role ?? '') == 'treasurer' ? 'selected' : '' ?>>Treasurer</option>
                                    <option value="admin" <?= ($user_role ?? '') == 'admin' ? 'selected' : '' ?>>Admin</option>
                                </select>
                                
                            </div>
                        </div>
                        <button type="submit" name="add_user" class="btn btn-danger mt-3">Create User</button>
                    </form>
                </div>
            </div>

            <!-- Users List -->
            <h5 class="mb-3">Existing Users</h5>
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Must Change Password</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
                    while ($row = $result->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($row['full_name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><span class="badge bg-secondary"><?= ucfirst($row['role']) ?></span></td>
                        <td><?= $row['must_change_password'] == 1 ? 'Yes' : 'No' ?></td>
                        <td><?= $row['created_at'] ?></td>
                       <td> 
    <a href="reset-password.php?id=<?= $row['id'] ?>" 
       class="btn btn-sm btn-warning"
       onclick="return confirm('Reset password for <?= htmlspecialchars($row['full_name']) ?>?\n\nThis will generate a new temporary password.')">
        Reset Password
    </a>
    
    <a href="user-delete.php?id=<?= $row['id'] ?>" 
       class="btn btn-sm btn-danger"
       onclick="return confirm('Permanently delete <?= htmlspecialchars($row['full_name']) ?>?\n\nThis action cannot be undone!')">
        Delete
    </a>
    <td>
</td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>