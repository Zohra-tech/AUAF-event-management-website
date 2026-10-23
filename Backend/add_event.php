<?php
session_start();

// Restrict access to admins only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. Admins only.");
}

include 'db_connect.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category = $_POST['category'];
    $date = $_POST['date'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $icon = $_POST['icon'];

    // Prepared statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO events (category, date, title, description, icon) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $category, $date, $title, $description, $icon);

    if ($stmt->execute()) {
        $message = "✅ Event added successfully!";
    } else {
        $message = "❌ Error: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Event</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">AUAF Events</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="pages/events.php">Events</a>
        </li>
        <?php if (isset($_SESSION['username'])): ?>
          <li class="nav-item">
            <span class="navbar-text text-light me-3">
              Welcome, <?= htmlspecialchars($_SESSION['username']) ?>
            </span>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="logout.php">Logout</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link" href="login.php">Login</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<h1 class="mb-4">Add New Event</h1>

<?php if ($message): ?>
    <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<form method="POST" action="add_event.php" class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Category</label>
        <input type="text" name="category" class="form-control" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Date</label>
        <input type="date" name="date" class="form-control" required>
    </div>
    <div class="col-12">
        <label class="form-label">Title</label>
        <input type="text" name="title" class="form-control" required>
    </div>
    <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="4" required></textarea>
    </div>
    <div class="col-12">
        <label class="form-label">Icon (Bootstrap Icon class)</label>
        <input type="text" name="icon" class="form-control" placeholder="e.g., bi-lightbulb" required>
    </div>
    <div class="col-12">
        <button type="submit" class="btn btn-gold">Add Event</button>
    </div>
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
