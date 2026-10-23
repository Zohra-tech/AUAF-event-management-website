<?php
session_start();

// Restrict access to admins only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. Admins only.");
}

include 'db_connect.php';

$message = "";
$event = [];

// Get event by ID
if (isset($_GET['id'])) {
    $event_id = intval($_GET['id']);
    $sql = "SELECT * FROM events WHERE event_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $event = $result->fetch_assoc();
}

// Handle update form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_id = intval($_POST['event_id']);
    $category = $_POST['category'];
    $date = $_POST['date'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $icon = $_POST['icon'];

    $stmt = $conn->prepare("UPDATE events SET category=?, date=?, title=?, description=?, icon=? WHERE event_id=?");
    $stmt->bind_param("sssssi", $category, $date, $title, $description, $icon, $event_id);

    if ($stmt->execute()) {
        $message = "✅ Event updated successfully!";
    } else {
        $message = "❌ Error: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Event</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">
    <h1>Edit Event</h1>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if (!empty($event)): ?>
    <form method="POST" action="edit_event.php" class="row g-3">
        <input type="hidden" name="event_id" value="<?= $event['event_id'] ?>">
        <div class="col-md-6">
            <label class="form-label">Category</label>
            <input type="text" name="category" class="form-control" value="<?= htmlspecialchars($event['category']) ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Date</label>
            <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($event['date']) ?>" required>
        </div>
        <div class="col-12">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($event['title']) ?>" required>
        </div>
        <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4" required><?= htmlspecialchars($event['description']) ?></textarea>
        </div>
        <div class="col-12">
            <label class="form-label">Icon (Bootstrap class)</label>
            <input type="text" name="icon" class="form-control" value="<?= htmlspecialchars($event['icon']) ?>" required>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-gold">Update Event</button>
        </div>
    </form>
    <?php else: ?>
        <p class="text-danger">Event not found.</p>
    <?php endif; ?>
</body>
</html>
