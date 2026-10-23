<?php
session_start();
include 'db_connect.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header("Location: pages/events.php");

        exit;
    } else {
        $message = "❌ Invalid credentials.";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Login</title></head>
<body>
<form method="POST" action="login.php">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
</form>
<p><?= htmlspecialchars($message) ?></p>
</body>
</html>
