<?php
session_start();
include 'db_connect.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // hash the password
    $role = $_POST['role'];

    // Prepared statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $password, $role);

    if ($stmt->execute()) {
        $message = "✅ User registered successfully!";
    } else {
        $message = "❌ Error: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">
    <h1 class="mb-4">Register New User</h1>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" action="register.php" class="row g-3">
        <div class="col-12">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" placeholder="Username" required>
        </div>
        <div class="col-12">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <div class="col-12">
            <label class="form-label">Role</label>
            <select name="role" class="form-select">
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-gold">Register</button>
        </div>
    </form>
</body>
</html>
