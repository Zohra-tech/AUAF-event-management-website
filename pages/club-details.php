<?php
require_once __DIR__ . '/../config/database.php';

$clubId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$clubId || $clubId < 1) {
    header('Location: /pages/clubs.php');
    exit;
}

try {
    // Retrieve the selected club.
    $stmt = $pdo->prepare(
        'SELECT club_id, club_name, description, google_space_url
         FROM clubs
         WHERE club_id = :club_id'
    );

    $stmt->execute(['club_id' => $clubId]);
    $club = $stmt->fetch();

    if (!$club) {
        http_response_code(404);
        exit('Club not found. Please return to the Student Clubs page.');
    }

    // Retrieve upcoming events belonging to this club only.
    $stmt = $pdo->prepare(
        "SELECT event_id, event_title, description, event_date,
                event_time, event_url, agenda
         FROM events
         WHERE club_id = :club_id
           AND status = 'upcoming'
           AND event_date >= CURDATE()
         ORDER BY event_date ASC, event_time ASC"
    );

    $stmt->execute(['club_id' => $clubId]);
    $events = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log('Club details query failed: ' . $e->getMessage());
    http_response_code(500);
    exit('Unable to load club details. Please contact the website administrator.');
}

function escapeClubDetail($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= escapeClubDetail($club['club_name']) ?> | AUAF Events</title>

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

<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark auaf-club-navbar">
    <div class="container">

        <a class="navbar-brand fw-bold" href="/index.php">
            AUAF EVENTS
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#clubDetailsNavbar"
            aria-controls="clubDetailsNavbar"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="clubDetailsNavbar">
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/index.php">Home</a>
                <a class="nav-link" href="/pages/events.php">Upcoming Events</a>
                <a class="nav-link active" href="/pages/clubs.php">Student Clubs</a>
                <a class="nav-link" href="/pages/about.php">About</a>
            </div>
        </div>

    </div>
</nav>

<!-- Club Hero -->
<header class="club-details-hero">
    <div class="container py-5">

        <a href="/pages/clubs.php"
           class="club-details-back-link d-inline-block mb-4">
            <i class="bi bi-arrow-left me-2"></i>
            Back to Student Clubs
        </a>

        <p class="club-details-eyebrow">
            AUAF STUDENT COMMUNITY
        </p>

        <h1 class="display-5 fw-bold text-white">
            <?= escapeClubDetail($club['club_name']) ?>
        </h1>

        <p class="club-details-subtitle">
            Discover this student organization, connect with its members,
            and explore its upcoming events.
        </p>

        <?php if (!empty($club['google_space_url'])): ?>
            <a
                href="<?= escapeClubDetail($club['google_space_url']) ?>"
                class="btn btn-warning btn-lg fw-semibold mt-3"
                target="_blank"
                rel="noopener noreferrer"
            >
                <i class="bi bi-people-fill me-2"></i>
                Join This Club
            </a>
        <?php endif; ?>

    </div>
</header>

<main class="container py-5">

    <!-- Club Information -->
    <section class="club-details-info card border-0 shadow-sm rounded-4 mb-5">
        <div class="card-body p-4 p-lg-5">

            <div class="row g-4 align-items-start">

                <div class="col-lg-8">
                    <p class="club-details-eyebrow">ABOUT THE CLUB</p>

                    <h2 class="h3 fw-bold mb-3">
                        Get to Know This Club
                    </h2>

                    <div class="club-details-description">
                        <?= nl2br(escapeClubDetail(
                            $club['description'] ?: 'No club description is available yet.'
                        )) ?>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="club-details-connect p-4 rounded-4">

                        <i class="bi bi-people-fill fs-2"></i>

                        <h3 class="h5 fw-bold mt-3">
                            Join Our Community
                        </h3>

                        <?php if (!empty($club['google_space_url'])): ?>

                            <p class="small">
                                Connect with this club through its Google Space.
                                Follow the joining instructions provided there.
                            </p>

                            <a
                                href="<?= escapeClubDetail($club['google_space_url']) ?>"
                                class="btn btn-warning fw-semibold w-100"
                                target="_blank"
                                rel="noopener noreferrer"
                            >
                                <i class="bi bi-box-arrow-up-right me-2"></i>
                                Join This Club
                            </a>

                        <?php else: ?>

                            <p class="small mb-0">
                                This club has not provided a Google Space link yet.
                                Please check back later.
                            </p>

                            <button
                                type="button"
                                class="btn btn-secondary w-100"
                                disabled
                            >
                                Join Link Unavailable
                            </button>

                        <?php endif; ?>

                        <p class="small text-muted mt-3 mb-0">
                            Joining a club through Google Space does not require
                            a separate registration on this website.
                        </p>

                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Upcoming Events -->
    <section class="club-details-events">

        <div class="mb-4">
            <p class="club-details-eyebrow">WHAT'S HAPPENING</p>

            <h2 class="display-6 fw-bold">
                Upcoming Events
            </h2>

            <p class="text-muted">
                Discover workshops, activities, and events organized by this club.
            </p>
        </div>

        <?php if (empty($events)): ?>

            <div class="club-details-empty text-center p-5 rounded-4">

                <i class="bi bi-calendar2-event display-3"></i>

                <h3 class="h4 fw-bold mt-3">
                    No Upcoming Events
                </h3>

                <p class="text-muted mb-0">
                    This club has no upcoming events listed right now.
                    Please check back later.
                </p>

            </div>

        <?php else: ?>

            <div class="row g-4">

                <?php foreach ($events as $event): ?>

                    <div class="col-md-6 col-xl-4">

                        <article class="card club-details-event-card h-100 border-0 shadow-sm rounded-4">

                            <div class="card-body p-4 d-flex flex-column">

                                <div class="club-details-date mb-3">
                                    <i class="bi bi-calendar-event me-2"></i>

                                    <?= escapeClubDetail($event['event_date']) ?>
                                </div>

                                <h3 class="h5 fw-bold">
                                    <?= escapeClubDetail($event['event_title']) ?>
                                </h3>

                                <p class="text-muted">
                                    <?= nl2br(escapeClubDetail($event['description'])) ?>
                                </p>

                                <div class="small mb-2">
                                    <i class="bi bi-clock me-2"></i>

                                    <?= escapeClubDetail(
                                        substr($event['event_time'], 0, 5)
                                    ) ?>
                                </div>

                                <div class="small mb-3">
                                    <i class="bi bi-geo-alt me-2"></i>

                                    <?= escapeClubDetail($event['event_url']) ?>
                                </div>

                                <?php if (!empty($event['agenda'])): ?>

                                    <details class="mb-3">
                                        <summary class="fw-semibold">
                                            View Agenda
                                        </summary>

                                        <p class="small text-muted mt-2">
                                            <?= nl2br(escapeClubDetail($event['agenda'])) ?>
                                        </p>
                                    </details>

                                <?php endif; ?>

                                <a
                                    href="/pages/register-event.php?id=<?= (int) $event['event_id'] ?>"
                                    class="btn btn-warning fw-semibold mt-auto"
                                >
                                    <i class="bi bi-ticket-perforated me-2"></i>
                                    Register for Event
                                </a>

                            </div>

                        </article>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </section>

</main>

<!-- Footer -->
<footer class="site-footer">
    <div class="container py-4">

        <div class="row gy-4">

            <div class="col-md-6">
                <a class="footer-brand" href="/index.php">
                    AUAF EVENTS
                </a>

                <p class="footer-description mt-2">
                    Discover university events, student organizations,
                    and opportunities to connect with the AUAF community.
                </p>
            </div>

            <div class="col-md-6 footer-explore">
                <h4>Explore</h4>

                <a href="/index.php">Home</a>
                <a href="/pages/events.php">Upcoming Events</a>
                <a href="/pages/clubs.php">Student Clubs</a>
            </div>

        </div>

        <div class="footer-bottom mt-4">
            <span>
                &copy; <?= date('Y') ?> AUAF Event Management Website
            </span>

            <span>Designed for the AUAF community</span>
        </div>

    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
