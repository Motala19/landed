<?php
// Simple Password Hasher Tool
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plain = $_POST['password'];
    $hashed = password_hash($plain, PASSWORD_DEFAULT);
    
    echo "<h3>Password you entered:</h3> <strong>" . htmlspecialchars($plain) . "</strong><br><br>";
    echo "<h3>Hashed Password (copy this):</h3> <code style='background:#f8f9fa; padding:10px; display:block; word-break:break-all;'>" . $hashed . "</code>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Password Hasher</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-5">
    <div class="container">
        <h2>Password Hasher Tool</h2>
        <form method="POST">
            <div class="mb-3">
                <label>Enter Password (e.g. Aegon@19)</label>
                <input type="text" name="password" class="form-control" value="Aegon@19" required>
            </div>
            <button type="submit" class="btn btn-primary">Generate Hashed Password</button>
        </form>
    </div>
</body>
</html>