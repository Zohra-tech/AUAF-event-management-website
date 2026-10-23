<?php
session_start();

// Restrict access to admins only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. Admins only.");
}

include 'db_connect.php';

$message = "";

if (isset($_GET['id'])) {
    $event_id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM events WHERE event_id = ?");
    $stmt->bind_param("i", $event_id);

    if ($stmt->execute()) {
        $message = "✅ Event deleted successfully!";
    } else {
        $message = "❌ Error: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Event</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">
    <h1>Delete Event</h1>
    <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <a href="pages/events.php" class="btn btn-secondary">Back to Events</a>
</body>
</html>
