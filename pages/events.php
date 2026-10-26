<?php
$pageTitle = 'Upcoming Events | AUAF';

require_once __DIR__ . '/../config/database.php';

$events = [];
$loadError = false;
$search = trim($_GET['search'] ?? '');

try {
    $sql = "
        SELECT
            e.event_id,
            e.event_title,
            e.description,
            e.event_date,
            e.event_time,
            e.event_url,
            c.club_id,
            c.club_name
        FROM events e
        INNER JOIN clubs c ON c.club_id = e.club_id
        WHERE e.status = 'upcoming'
          AND e.event_date >= CURDATE()
    ";

    $params = [];

    if ($search !== '') {
        $sql .= "
            AND (
                e.event_title LIKE :title_search
                OR e.description LIKE :description_search
                OR e.location LIKE :location_search
                OR c.club_name LIKE :club_search
            )
        ";

        $searchTerm = '%' . $search . '%';

        $params = [
            'title_search' => $searchTerm,
            'description_search' => $searchTerm,
            'location_search' => $searchTerm,
            'club_search' => $searchTerm
        ];
    }

    $sql .= " ORDER BY e.event_date ASC, e.event_time ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $events = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log('Unable to load events: ' . $e->getMessage());
    $loadError = true;
}

$eventUrl = trim($_POST['event_url'] ?? '');

if (
    !filter_var($eventUrl, FILTER_VALIDATE_URL) ||
    !in_array(
        strtolower((string) parse_url($eventUrl, PHP_URL_SCHEME)),
        ['http', 'https'],
        true
    )
) {
    $errors[] = 'Please enter a valid virtual event URL.';
}

function escapeEventPage($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= escapeEventPage($pageTitle) ?></title>

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
            data-bs-target="#eventsNavbar"
            aria-controls="eventsNavbar"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="eventsNavbar">
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/index.php">Home</a>
                <a class="nav-link active" href="/pages/events.php">Upcoming Events</a>
                <a class="nav-link" href="/pages/clubs.php">Student Clubs</a>
                <a class="nav-link" href="/pages/about.php">About</a>
            </div>
        </div>

    </div>
</nav>

<!-- Hero -->
<header class="events-page-hero">
    <div class="container py-5">

        <p class="events-eyebrow">DISCOVER YOUR CAMPUS</p>

        <h1 class="display-4 fw-bold text-white">
            Upcoming Events
        </h1>

        <p class="events-hero-description">
            Explore workshops, student activities, and campus events
            organized by AUAF student clubs.
        </p>

        <a href="/pages/clubs.php" class="btn btn-warning fw-semibold mt-3">
            <i class="bi bi-people-fill me-2"></i>
            Explore Student Clubs
        </a>

    </div>
</header>

<main class="container py-5">

    <!-- Search -->
    <section class="events-search-section mb-5">

        <div class="row align-items-end g-3">

            <div class="col-lg-7">
                <p class="events-section-eyebrow">FIND YOUR NEXT EXPERIENCE</p>

                <h2 class="h3 fw-bold mb-2">
                    Find an Event
                </h2>

                <p class="text-muted mb-0">
                    Search by event name, club, description, or location.
                </p>
            </div>

            <div class="col-lg-5">

                <form method="GET" action="/pages/events.php">
                    <label for="eventSearch" class="form-label fw-semibold">
                        Search events
                    </label>

                    <div class="input-group input-group-lg">

                        <span class="input-group-text bg-white">
                            <i class="bi bi-search"></i>
                        </span>

                        <input
                            type="search"
                            class="form-control"
                            id="eventSearch"
                            name="search"
                            placeholder="Search events or clubs..."
                            value="<?= escapeEventPage($search) ?>"
                        >

                        <button type="submit" class="btn btn-warning fw-semibold">
                            Search
                        </button>

                    </div>
                </form>

            </div>

        </div>

        <?php if ($search !== ''): ?>
            <div class="mt-3">
                <span class="text-muted">
                    Search results for:
                    <strong><?= escapeEventPage($search) ?></strong>
                </span>

                <a href="/pages/events.php" class="events-clear-search ms-2">
                    Clear search
                </a>
            </div>
        <?php endif; ?>

    </section>

    <!-- Event Results -->
    <section>

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">

            <h2 class="h3 fw-bold mb-0">
                <?= $search !== '' ? 'Search Results' : 'All Upcoming Events' ?>
            </h2>

            <?php if (!$loadError): ?>
                <span class="events-count">
                    <?= count($events) ?>
                    <?= count($events) === 1 ? 'event' : 'events' ?>
                    found
                </span>
            <?php endif; ?>

        </div>

        <?php if ($loadError): ?>

            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Events could not be loaded right now. Please try again later.
            </div>

        <?php elseif (empty($events)): ?>

            <div class="events-empty-state text-center rounded-4 p-5">

                <i class="bi bi-calendar2-event display-3"></i>

                <h3 class="h4 fw-bold mt-3">
                    <?= $search !== '' ? 'No Matching Events' : 'No Upcoming Events Yet' ?>
                </h3>

                <p class="text-muted mb-4">
                    <?php if ($search !== ''): ?>
                        We couldn't find events matching your search.
                        Try another keyword.
                    <?php else: ?>
                        There are no upcoming events available at the moment.
                        Please check back later.
                    <?php endif; ?>
                </p>

                <?php if ($search !== ''): ?>
                    <a href="/pages/events.php" class="btn btn-warning fw-semibold">
                        View All Events
                    </a>
                <?php else: ?>
                    <a href="/pages/clubs.php" class="btn btn-warning fw-semibold">
                        Explore Student Clubs
                    </a>
                <?php endif; ?>

            </div>

        <?php else: ?>

            <div class="row g-4">

                <?php foreach ($events as $event): ?>

                    <div class="col-md-6 col-xl-4">

                        <article class="card global-event-card h-100 border-0 shadow-sm rounded-4">

                            <div class="global-event-card-top"></div>

                            <div class="card-body p-4 d-flex flex-column">

                                <div class="global-event-date mb-3">
                                    <i class="bi bi-calendar-event me-2"></i>
                                    <?= date('M j, Y', strtotime($event['event_date'])) ?>
                                </div>

                                <h3 class="h5 fw-bold">
                                    <?= escapeEventPage($event['event_title']) ?>
                                </h3>

                                <p class="global-event-club">
                                    <i class="bi bi-people me-2"></i>
                                    <?= escapeEventPage($event['club_name']) ?>
                                </p>

                                <p class="text-muted global-event-description">
                                    <?= escapeEventPage($event['description']) ?>
                                </p>

                                <div class="global-event-meta small mb-2">
                                    <i class="bi bi-clock me-2"></i>
                                    <?= date('g:i A', strtotime($event['event_time'])) ?>
                                </div>

                                <div class="global-event-meta small mb-4">
                                    <i class="bi bi-geo-alt me-2"></i>
                                    <?= escapeEventPage($event['location']) ?>
                                </div>

                                <div class="mt-auto d-grid gap-2">

                                    <a
                                        href="/pages/register-event.php?id=<?= (int) $event['event_id'] ?>"
                                        class="btn btn-warning fw-semibold"
                                    >
                                        <i class="bi bi-ticket-perforated me-2"></i>
                                        Register for Event
                                    </a>

                                    <a
                                        href="/pages/club-details.php?id=<?= (int) $event['club_id'] ?>"
                                        class="btn btn-outline-secondary"
                                    >
                                        View Club
                                    </a>

                                </div>

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
            <span>&copy; <?= date('Y') ?> AUAF Event Management Website</span>
            <span>Designed for the AUAF community</span>
        </div>

    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
