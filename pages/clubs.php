<?php
$pageTitle = 'Student Clubs | AUAF';
require_once __DIR__ . '/../config/database.php';

$clubs = [];
$clubsLoadError = false;

try {
    $stmt = $pdo->query(
        'SELECT club_id, club_name, description, google_space_url
         FROM clubs
         ORDER BY club_name ASC'
    );

    $clubs = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('Unable to retrieve clubs: ' . $e->getMessage());
    $clubsLoadError = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>

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

<body class="clubs-page">

<!-- Navigation -->
<nav class="site-navbar">
    <div class="container d-flex justify-content-between align-items-center flex-wrap gap-3">

        <a class="brand" href="/index.php">AUAF EVENTS</a>

        <div class="nav-links">
            <a href="/index.php">Home</a>
            <a href="/pages/events.php">Events</a>
            <a href="/pages/clubs.php" class="active">Student Clubs</a>
            <a href="/pages/about.php">About</a>
            <a href="/club/login.php">Club Sign In</a>
        </div>

    </div>
</nav>

<!-- Hero Section -->
<section class="clubs-page-hero">
    <div class="container">

        <div class="clubs-breadcrumb">
            <a href="/index.php">Home</a>
            <i class="bi bi-chevron-right"></i>
            <span>Student Clubs</span>
        </div>

        <span class="section-eyebrow">DISCOVER YOUR COMMUNITY</span>

        <h1>Find Your People.<br>Discover Your Club.</h1>

        <p>
            Explore student organizations, connect with fellow students,
            and discover opportunities to learn, lead, and participate
            in the AUAF community.
        </p>

    </div>
</section>

<!-- Clubs Section -->
<section class="clubs-browser">
    <div class="container">

        <div class="clubs-heading">
            <div>
                <span class="section-eyebrow">STUDENT ORGANIZATIONS</span>
                <h2 class="section-title">Explore Student Clubs</h2>
                <p>
                    Find a community that matches your interests and goals.
                </p>
            </div>

            <div class="club-total">
                <i class="bi bi-people"></i>
                <?= count($clubs) ?> clubs
            </div>
        </div>

        <!-- Club Cards -->
        <div class="club-grid">

            <?php if ($clubsLoadError): ?>

                <div class="clubs-note">
                    <i class="bi bi-exclamation-circle"></i>
                    Unable to load clubs right now. Please try again later.
                </div>

            <?php elseif (empty($clubs)): ?>

                <div class="no-clubs">
                    <i class="bi bi-people"></i>
                    <h3>No clubs registered yet</h3>
                    <p>
                        Registered student clubs will appear here.
                        Please check back later.
                    </p>
                </div>

            <?php else: ?>

                <?php foreach ($clubs as $club): ?>

                    <article class="club-card">

                        <div class="club-card-icon">
                            <i class="bi bi-people"></i>
                        </div>

                        <span class="club-category">
                            AUAF Student Club
                        </span>

                        <h3>
                            <?= htmlspecialchars(
                                $club['club_name'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </h3>

                        <p>
                            <?= htmlspecialchars(
                                $club['description']
                                    ?? 'Learn more about this student club.',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>

                        <a
                            class="club-link"
                            href="/pages/club-details.php?id=<?= (int) $club['club_id'] ?>"
                        >
                            Explore Club
                            <i class="bi bi-arrow-right"></i>
                        </a>

                        <?php if (!empty($club['google_space_url'])): ?>
                            <a
                                class="club-space-link"
                                href="<?= htmlspecialchars(
                                    $club['google_space_url'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                target="_blank"
                                rel="noopener noreferrer"
                            >
                                Visit Google Space
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                        <?php endif; ?>

                    </article>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>

    </div>
</section>

<!-- Footer -->
<footer class="site-footer">
    <div class="container">

        <div class="row gy-4">

            <div class="col-lg-6">
                <a class="footer-brand" href="/index.php">
                    AUAF EVENTS
                </a>

                <p class="footer-description">
                    Discover university events, student organizations,
                    and opportunities to connect with the AUAF community.
                </p>
            </div>

            <div class="col-12 col-lg-6 footer-explore">
                <h4>Explore</h4>
                <a href="/index.php">Home</a>
                <a href="/pages/events.php">Upcoming Events</a>
                <a href="/pages/clubs.php">Student Clubs</a>
            </div>

        </div>

        <div class="footer-bottom">
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
