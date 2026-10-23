<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}
include '../db_connect.php';

// Page title
$pageTitle = "Upcoming Events | AUAF";

// Handle search input
$keyword = $_GET['keyword'] ?? '';
$sql = "SELECT * FROM events WHERE title LIKE ? OR category LIKE ? ORDER BY date ASC";
$stmt = $conn->prepare($sql);
$searchTerm = "%$keyword%";
$stmt->bind_param("ss", $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

// Collect events
$events = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="../index.php">AUAF Events</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link active" href="events.php">Events</a>
        </li>
        <?php if (isset($_SESSION['username'])): ?>
          <li class="nav-item">
            <span class="navbar-text text-light me-3">
              Welcome, <?= htmlspecialchars($_SESSION['username']) ?>
            </span>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="../logout.php">Logout</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link" href="../login.php">Login</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<div class="container py-5">

    <!-- Page Title + Add Event Button (restricted to admins) -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0"><?= htmlspecialchars($pageTitle) ?></h1>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <a href="../add_event.php" class="btn btn-gold">+ Add Event</a>
        <?php endif; ?>
    </div>

    <!-- Search Form -->
    <form method="GET" action="events.php" class="mb-4 d-flex gap-2">
        <input type="text" name="keyword" class="form-control" placeholder="Search events..." value="<?= htmlspecialchars($keyword) ?>">
        <button type="submit" class="btn btn-gold">Search</button>
    </form>

    <!-- Events Grid -->
    <div class="row g-4">
        <?php if (!empty($events)): ?>
            <?php foreach ($events as $event): ?>
                <div class="col-md-6 col-lg-4">
                    <article class="event-card">
                        <div class="event-card-art">
                            <div class="event-art-icon">
                                <i class="bi <?= htmlspecialchars($event['icon']) ?>"></i>
                            </div>
                            <span class="event-category">
                                <?= htmlspecialchars($event['category']) ?>
                            </span>
                        </div>
                        <div class="event-card-body">
                            <div class="event-date">
                                <i class="bi bi-calendar3"></i>
                                <?= htmlspecialchars($event['date']) ?>
                            </div>
                            <h3><?= htmlspecialchars($event['title']) ?></h3>
                            <p><?= htmlspecialchars($event['description']) ?></p>

                            <!-- Restrict Edit/Delete to admins -->
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                <div class="mt-3 d-flex gap-2">
                                    <a href="../edit_event.php?id=<?= $event['event_id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="../delete_event.php?id=<?= $event['event_id'] ?>" class="btn btn-sm btn-danger"
                                       onclick="return confirm('Are you sure you want to delete this event?');">Delete</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted">No events found.</p>
        <?php endif; ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
