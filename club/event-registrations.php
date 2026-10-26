<?php
session_start();

require_once __DIR__ . '/../config/database.php';

// Require club login.
if (empty($_SESSION['club_id'])) {
    header('Location: /club/login.php');
    exit;
}

$clubId = (int) $_SESSION['club_id'];
$eventId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$eventId || $eventId < 1) {
    header('Location: /club/manage-events.php');
    exit;
}

$event = null;
$registrations = [];
$error = '';

try {
    // Confirm this event belongs to the logged-in club.
    $stmt = $pdo->prepare(
        'SELECT event_id, event_title, event_date, event_time, event_url
         FROM events
         WHERE event_id = :event_id
           AND club_id = :club_id
         LIMIT 1'
    );

    $stmt->execute([
        'event_id' => $eventId,
        'club_id' => $clubId
    ]);

    $event = $stmt->fetch();

    if (!$event) {
        http_response_code(404);
        exit('Event not found or you do not have permission to view its registrations.');
    }

    // Retrieve registrations belonging to this event.
    $stmt = $pdo->prepare(
        "SELECT
            registration_id,
            first_name,
            last_name,
            student_email,
            registered_at
         FROM event_registrations
         WHERE event_id = :event_id
           AND status = 'registered'
         ORDER BY registered_at DESC"
    );

    $stmt->execute(['event_id' => $eventId]);
    $registrations = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log('Unable to load event registrations: ' . $e->getMessage());
    $error = 'Unable to load registrations right now. Please try again later.';
}

function escapeRegistrationList($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Event Registrations | AUAF Events</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body class="club-dashboard-page">

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark club-dashboard-navbar">
    <div class="container">

        <a class="navbar-brand fw-bold" href="/club/dashboard.php">
            AUAF EVENTS
            <span class="dashboard-brand-label">CLUB PORTAL</span>
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#registrationsNavbar"
            aria-controls="registrationsNavbar"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="registrationsNavbar">
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/club/dashboard.php">Dashboard</a>
                <a class="nav-link" href="/club/create-event.php">Create Event</a>
                <a class="nav-link active" href="/club/manage-events.php">Manage Events</a>
                <a class="nav-link" href="/pages/events.php">View Website</a>
                <a class="nav-link" href="/club/logout.php">Log Out</a>
            </div>
        </div>

    </div>
</nav>

<!-- Header -->
<header class="club-dashboard-hero">
    <div class="container py-5">

        <a href="/club/manage-events.php" class="registration-list-back">
            <i class="bi bi-arrow-left me-2"></i>
            Back to Manage Events
        </a>

        <p class="dashboard-eyebrow mt-4">EVENT MANAGEMENT</p>

        <h1 class="display-5 fw-bold text-white">
            Student Registrations
        </h1>

        <p class="dashboard-hero-text">
            View the students registered for this event.
        </p>

    </div>
</header>

<main class="container py-5">

    <!-- Event summary -->
    <section class="registration-list-summary card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">

            <div class="row g-3 align-items-center">

                <div class="col-lg-8">
                    <p class="dashboard-section-eyebrow mb-2">SELECTED EVENT</p>

                    <h2 class="h3 fw-bold mb-3">
                        <?= escapeRegistrationList($event['event_title']) ?>
                    </h2>

                    <p class="text-muted mb-2">
                        <i class="bi bi-calendar-event me-2"></i>
                        <?= date('F j, Y', strtotime($event['event_date'])) ?>
                    </p>

                    <p class="text-muted mb-2">
                        <i class="bi bi-clock me-2"></i>
                        <?= date('g:i A', strtotime($event['event_time'])) ?>
                    </p>

                    <p class="text-muted mb-0">
                        <i class="bi bi-geo-alt me-2"></i>
                        <?= escapeRegistrationList($event['event_url']) ?>
                    </p>
                </div>

                <div class="col-lg-4">
                    <div class="registration-total-box text-center p-4 rounded-4">
                        <i class="bi bi-people fs-2"></i>

                        <p class="registration-total-number mb-0">
                            <?= count($registrations) ?>
                        </p>

                        <p class="mb-0">Registered Students</p>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- Registration list -->
    <section class="registration-list-panel">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

            <div>
                <p class="dashboard-section-eyebrow">ATTENDEE INFORMATION</p>
                <h2 class="h3 fw-bold mb-0">Registration List</h2>
            </div>

            <a
                href="/club/manage-events.php"
                class="btn btn-outline-dark"
            >
                <i class="bi bi-arrow-left me-2"></i>
                Manage Events
            </a>

        </div>

        <?php if ($error !== ''): ?>

            <div class="alert alert-danger">
                <?= escapeRegistrationList($error) ?>
            </div>

        <?php elseif (empty($registrations)): ?>

            <div class="dashboard-empty-state text-center p-5 rounded-4">

                <i class="bi bi-person-plus display-3"></i>

                <h3 class="h4 fw-bold mt-3">
                    No Registrations Yet
                </h3>

                <p class="text-muted mb-0">
                    No students have registered for this event yet.
                    Check back later for new registrations.
                </p>

            </div>

        <?php else: ?>

            <div class="registration-list-table table-responsive">

                <table class="table align-middle mb-0">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student Name</th>
                            <th>AUAF Email</th>
                            <th>Registration Date</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($registrations as $index => $registration): ?>

                            <tr>
                                <td><?= $index + 1 ?></td>

                                <td class="fw-semibold">
                                    <?= escapeRegistrationList(
                                        $registration['first_name'] . ' ' .
                                        $registration['last_name']
                                    ) ?>
                                </td>

                                <td>
                                    <a
                                        href="mailto:<?= escapeRegistrationList($registration['student_email']) ?>"
                                        class="registration-student-email"
                                    >
                                        <?= escapeRegistrationList($registration['student_email']) ?>
                                    </a>
                                </td>

                                <td>
                                    <?= date(
                                        'M j, Y g:i A',
                                        strtotime($registration['registered_at'])
                                    ) ?>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

            <p class="small text-muted mt-3">
                Student information is available only to the club that owns this event.
                Handle student information responsibly.
            </p>

        <?php endif; ?>

    </section>

</main>

<footer class="site-footer">
    <div class="container py-4">

        <div class="row gy-4">

            <div class="col-md-6">
                <a class="footer-brand" href="/index.php">AUAF EVENTS</a>

                <p class="footer-description mt-2">
                    Manage your events and connect with the AUAF community.
                </p>
            </div>

            <div class="col-md-6 footer-explore">
                <h4>Club Portal</h4>
                <a href="/club/dashboard.php">Dashboard</a>
                <a href="/club/manage-events.php">Manage Events</a>
            </div>

        </div>

        <div class="footer-bottom mt-4">
            <span>&copy; <?= date('Y') ?> AUAF Event Management Website</span>
            <span>Club Management Portal</span>
        </div>

    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
