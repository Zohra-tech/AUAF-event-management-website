<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// Only signed-in clubs can manage events.
if (empty($_SESSION['club_id'])) {
    header('Location: /club/login.php');
    exit;
}

$clubId = (int) $_SESSION['club_id'];
$clubName = $_SESSION['club_name'] ?? 'Your Student Club';

$success = '';
$error = '';
$events = [];

// Create a CSRF token for form submissions.
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Success messages after creating, editing, or cancelling.
if (isset($_GET['created'])) {
    $success = 'Your event has been created successfully.';
} elseif (isset($_GET['updated'])) {
    $success = 'Your event has been updated successfully.';
} elseif (isset($_GET['cancelled'])) {
    $success = 'Your event has been cancelled successfully.';
}

// Process cancellation.
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && ($_POST['action'] ?? '') === 'cancel'
) {
    $postedToken = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['csrf_token'], $postedToken)) {
        $error = 'Your request could not be verified. Please refresh the page and try again.';
    } else {
        $eventId = filter_input(
            INPUT_POST,
            'event_id',
            FILTER_VALIDATE_INT
        );

        if (!$eventId || $eventId < 1) {
            $error = 'Invalid event selected.';
        } else {
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
        }
    }
}

// Retrieve only this club's events and their registration counts.
try {
    $stmt = $pdo->prepare(
        "SELECT
            e.event_id,
            e.event_title,
            e.description,
            e.event_date,
            e.event_time,
            e.event_url,
            e.agenda,
            e.status,
            (
                SELECT COUNT(*)
                FROM event_registrations r
                WHERE r.event_id = e.event_id
                  AND r.status = 'registered'
            ) AS registration_count
         FROM events e
         WHERE e.club_id = :club_id
         ORDER BY
            CASE WHEN e.status = 'upcoming' THEN 0 ELSE 1 END,
            e.event_date ASC,
            e.event_time ASC"
    );

    $stmt->execute(['club_id' => $clubId]);
    $events = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log('Unable to load club events: ' . $e->getMessage());
    $error = 'Unable to load your events. Please check the database configuration.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Manage Events | AUAF Events</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >
    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark" style="background:#101d36;">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/club/dashboard.php">
            AUAF EVENTS
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#manageNavbar"
            aria-controls="manageNavbar"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="manageNavbar">
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/club/dashboard.php">Dashboard</a>
                <a class="nav-link" href="/club/create-event.php">Create Event</a>
                <a class="nav-link" href="/pages/events.php">Public Events</a>
                <a class="nav-link" href="/pages/clubs.php">Student Clubs</a>
            </div>
        </div>
    </div>
</nav>

<header class="py-5" style="background:#101d36; color:white;">
    <div class="container">
        <p class="text-uppercase mb-2" style="color:#e2b85b; letter-spacing:2px;">
            Club Dashboard
        </p>

        <h1 class="display-5 fw-bold">Manage Your Events</h1>

        <p class="mb-0">
            Welcome, <?= htmlspecialchars($clubName, ENT_QUOTES, 'UTF-8') ?>.
            View, edit, and manage your club's events.
        </p>
    </div>
</header>

<main class="container py-5">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Your Events</h2>
            <p class="text-muted mb-0">
                Manage your upcoming activities and review student registrations.
            </p>
        </div>

        <a href="/club/create-event.php"
           class="btn btn-warning fw-semibold">
            <i class="bi bi-plus-circle me-1"></i>
            Create Event
        </a>
    </div>

    <?php if ($success !== ''): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>

            <button type="button" class="btn-close"
                    data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <div class="alert alert-danger" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <?php if (empty($events)): ?>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body text-center py-5 px-4">
                <i class="bi bi-calendar2-event display-3"
                   style="color:#b18a36;"></i>

                <h3 class="fw-bold mt-3">No Events Yet</h3>

                <p class="text-muted">
                    Events you create will appear here so you can manage them.
                </p>

                <a href="/club/create-event.php"
                   class="btn btn-warning fw-semibold">
                    Prepare Your First Event
                </a>
            </div>
        </div>

    <?php else: ?>

        <div class="row g-4">

            <?php foreach ($events as $event): ?>
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">

                            <div class="d-flex flex-wrap justify-content-between gap-3">

                                <div class="flex-grow-1">
                                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">

                                        <h3 class="h5 fw-bold mb-0">
                                            <?= htmlspecialchars(
                                                $event['event_title'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </h3>

                                        <?php if ($event['status'] === 'cancelled'): ?>
                                            <span class="badge text-bg-secondary">
                                                Cancelled
                                            </span>
                                        <?php else: ?>
                                            <span class="badge"
                                                  style="background:#e8f5e9;color:#246b38;">
                                                Upcoming
                                            </span>
                                        <?php endif; ?>

                                    </div>

                                    <p class="text-muted mb-3">
                                        <?= nl2br(htmlspecialchars(
                                            $event['description'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        )) ?>
                                    </p>

                                    <div class="row g-2 small">
                                        <div class="col-12 col-md-6">
                                            <i class="bi bi-calendar3 me-2"
                                               style="color:#a47b25;"></i>

                                            <?= htmlspecialchars(
                                                $event['event_date'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <i class="bi bi-clock me-2"
                                               style="color:#a47b25;"></i>

                                            <?= htmlspecialchars(
                                                substr($event['event_time'], 0, 5),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <i class="bi bi-geo-alt me-2"
                                               style="color:#a47b25;"></i>

                                            <?= htmlspecialchars(
                                                $event['location'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <i class="bi bi-people me-2"
                                               style="color:#a47b25;"></i>

                                            <?= (int) $event['registration_count'] ?>
                                            student registration(s)
                                        </div>
                                    </div>

                                    <?php if (!empty($event['agenda'])): ?>
                                        <details class="mt-3">
                                            <summary class="fw-semibold">
                                                View agenda
                                            </summary>

                                            <p class="mt-2 mb-0 text-muted">
                                                <?= nl2br(htmlspecialchars(
                                                    $event['agenda'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                )) ?>
                                            </p>
                                        </details>
                                    <?php endif; ?>
                                </div>

                                <div class="d-flex flex-wrap align-items-start gap-2">

                                    <?php if ($event['status'] === 'upcoming'): ?>

                                        <a
                                            class="btn btn-outline-primary btn-sm"
                                            href="/club/edit-event.php?id=<?= (int) $event['event_id'] ?>"
                                        >
                                            <i class="bi bi-pencil me-1"></i>
                                            Edit
                                        </a>

                                    <?php endif; ?>

                                    <!-- View Registrations button -->
                                    <a
                                        class="btn btn-outline-primary btn-sm"
                                        href="/club/event-registrations.php?id=<?= (int) $event['event_id'] ?>"
                                    >
                                        <i class="bi bi-people me-1"></i>
                                        View Registrations
                                    </a>

                                    <?php if ($event['status'] === 'upcoming'): ?>

                                        <form
                                            method="post"
                                            action="/club/manage-events.php"
                                            onsubmit="return confirm('Are you sure you want to cancel this event?');"
                                        >
                                            <input
                                                type="hidden"
                                                name="action"
                                                value="cancel"
                                            >

                                            <input
                                                type="hidden"
                                                name="event_id"
                                                value="<?= (int) $event['event_id'] ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="csrf_token"
                                                value="<?= htmlspecialchars(
                                                    $_SESSION['csrf_token'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>"
                                            >

                                            <button
                                                type="submit"
                                                class="btn btn-outline-danger btn-sm"
                                            >
                                                <i class="bi bi-x-circle me-1"></i>
                                                Cancel Event
                                            </button>
                                        </form>

                                    <?php else: ?>

                                        <span class="text-muted small">
                                            This event has been cancelled.
                                        </span>

                                    <?php endif; ?>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</main>

<footer class="site-footer">
    <div class="container py-3">
        <span>
            &copy; <?= date('Y') ?> AUAF Event Management Website
        </span>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>