<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (empty($_SESSION['club_id'])) {
    header('Location: /club/login.php');
    exit;
}

if (isset($_GET['updated'])) {
    $success = 'Your event has been updated successfully.';
}

$clubId = (int) $_SESSION['club_id'];
$error = '';
$success = isset($_GET['created'])
    ? 'Your event has been saved successfully.'
    : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
    ($_POST['action'] ?? '') === 'cancel') {
    $eventId = filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT);

    if ($eventId) {
        try {
            $stmt = $pdo->prepare(
                "UPDATE events
                 SET status = 'cancelled'
                 WHERE event_id = :event_id
                   AND club_id = :club_id
                   AND status = 'upcoming'"
            );

            $stmt->execute([
                'event_id' => $eventId,
                'club_id' => $clubId,
            ]);

            if ($stmt->rowCount() > 0) {
                header('Location: /club/manage-events.php?cancelled=1');
                exit;
            }

            $error = 'The event could not be cancelled. It may already be cancelled or may not belong to your club.';
        } catch (PDOException $e) {
            error_log('Event cancellation failed: ' . $e->getMessage());
            $error = 'Unable to cancel the event. Please try again.';
        }
    } else {
        $error = 'Invalid event selected.';
    }
}

if (isset($_GET['cancelled'])) {
    $success = 'The event has been cancelled.';
}

try {
    $stmt = $pdo->prepare(
        'SELECT e.event_id, e.event_title, e.description,
                e.event_date, e.event_time, e.location, e.agenda,
                e.status,
                (SELECT COUNT(*)
                 FROM event_registrations r
                 WHERE r.event_id = e.event_id
                   AND r.status = \'registered\') AS registration_count
         FROM events e
         WHERE e.club_id = :club_id
         ORDER BY e.event_date DESC, e.event_time DESC'
    );

    $stmt->execute(['club_id' => $clubId]);
    $events = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('Event listing failed: ' . $e->getMessage());
    $events = [];
    $error = 'Unable to load your events. Please check the database configuration.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Events | AUAF Events</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark" style="background:#101d36">
    <div class="container">
        <a class="navbar-brand" href="/club/dashboard.php">AUAF EVENTS</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="/club/dashboard.php">Dashboard</a>
            <a class="nav-link" href="/club/create-event.php">Create Event</a>
            <a class="nav-link" href="/pages/events.php">Public Events</a>
        </div>
    </div>
</nav>

<main class="container py-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1>Manage Events</h1>
            <p>View and manage events created by your club.</p>
        </div>
        <a class="btn btn-warning" href="/club/create-event.php">
            <i class="bi bi-plus-circle"></i> Create Event
        </a>
    </div>

    <?php if ($success !== ''): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if (!$events): ?>
        <div class="p-5 text-center border rounded-4">
            <h2>No events yet</h2>
            <p>Your events will appear here after you create and save them.</p>
            <a class="btn btn-warning" href="/club/create-event.php">
                Create Your First Event
            </a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Location</th>
                        <th>Registrations</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($events as $event): ?>
                    <tr>
                        <td>
                            <strong>
                                <?= htmlspecialchars($event['event_title']) ?>
                            </strong>
                            <div class="small text-muted">
                                <?= htmlspecialchars($event['description']) ?>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($event['event_date']) ?></td>
                        <td><?= htmlspecialchars(substr($event['event_time'], 0, 5)) ?></td>
                        <td><?= htmlspecialchars($event['location']) ?></td>
                        <td><?= (int) $event['registration_count'] ?></td>
                        <td><?= htmlspecialchars(ucfirst($event['status'])) ?></td>
                        <td>
                            <?php if ($event['status'] === 'upcoming'): ?>
                                <a class="btn btn-sm btn-outline-primary mb-1"
                                   href="/club/edit-event.php?id=<?= (int) $event['event_id'] ?>">
                                    Edit
                                </a>

                                <form method="post"
                                      onsubmit="return confirm('Cancel this event?');"
                                      class="d-inline">
                                    <input type="hidden" name="action" value="cancel">
                                    <input type="hidden" name="event_id"
                                           value="<?= (int) $event['event_id'] ?>">
                                    <button type="submit"
                                            class="btn btn-sm btn-outline-danger mb-1">
                                        Cancel
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted">No actions</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>

<footer class="site-footer">
    <div class="container py-3">
        <span>&copy; <?= date('Y') ?> AUAF Event Management Website</span>
    </div>
</footer>
</body>
</html>
