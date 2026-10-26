<?php
declare(strict_types=1);

session_start();

// Only signed-in clubs can access this page.
if (empty($_SESSION['club_id'])) {
    header('Location: /pages/club-login.php');
    exit;
}

$clubId = (int) $_SESSION['club_id'];
$clubName = $_SESSION['club_name'] ?? 'Club Dashboard';
$pageTitle = 'Club Dashboard | AUAF Events';

require_once __DIR__ . '/../config/database.php';

// Count this club's events.
$eventStmt = $pdo->prepare(
    'SELECT COUNT(*) FROM events WHERE club_id = :club_id'
);
$eventStmt->execute([':club_id' => $clubId]);
$totalEvents = (int) $eventStmt->fetchColumn();

// Count registrations for this club's events.
$registrationStmt = $pdo->prepare(
    'SELECT COUNT(*)
     FROM event_registrations er
     INNER JOIN events e ON er.event_id = e.event_id
     WHERE e.club_id = :club_id'
);
$registrationStmt->execute([':club_id' => $clubId]);
$totalRegistrations = (int) $registrationStmt->fetchColumn();

// Retrieve only this club's events and registration counts.
$listStmt = $pdo->prepare(
    'SELECT
        e.event_id,
        e.title,
        e.event_date,
        e.start_time,
        e.location,
        e.status,
        COUNT(er.registration_id) AS registration_count
     FROM events e
     LEFT JOIN event_registrations er ON er.event_id = e.event_id
     WHERE e.club_id = :club_id
     GROUP BY
        e.event_id, e.title, e.event_date,
        e.start_time, e.location, e.status
     ORDER BY e.event_date DESC, e.start_time DESC'
);
$listStmt->execute([':club_id' => $clubId]);
$events = $listStmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<main class="container py-5">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <p class="text-secondary mb-1">CLUB MANAGEMENT</p>
            <h1 style="color:#10294a;font-weight:800;">
                Welcome, <?= htmlspecialchars($clubName, ENT_QUOTES, 'UTF-8') ?>
            </h1>
            <p class="text-secondary mb-0">
                Manage your events and track student registrations.
            </p>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <a href="/club/add-event.php" class="btn btn-gold">
                + Add Event
            </a>
            <a href="/pages/club-logout.php" class="btn btn-outline-secondary">
                Sign Out
            </a>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <section class="feature-card">
                <p class="text-secondary mb-1">Total Events</p>
                <h2><?= $totalEvents ?></h2>
            </section>
        </div>

        <div class="col-md-6">
            <section class="feature-card">
                <p class="text-secondary mb-1">Student Registrations</p>
                <h2><?= $totalRegistrations ?></h2>
            </section>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Your Events</h2>
        <a href="/pages/events.php">View public events →</a>
    </div>

    <?php if (!$events): ?>
        <section class="feature-card text-center">
            <h3>No events yet</h3>
            <p class="text-secondary">
                Create your first event to let AUAF students discover it.
            </p>
            <a href="/club/add-event.php" class="btn btn-gold">
                Create Your First Event
            </a>
        </section>
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
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($events as $event): ?>
                        <tr>
                            <td>
                                <strong>
                                    <?= htmlspecialchars($event['title'], ENT_QUOTES, 'UTF-8') ?>
                                </strong>
                                <div class="small text-secondary">
                                    <?= htmlspecialchars($event['status'] ?? 'published', ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            </td>
                            <td>
                                <?= htmlspecialchars($event['event_date'], ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($event['start_time'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($event['location'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td>
                                <strong><?= (int) $event['registration_count'] ?></strong>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
