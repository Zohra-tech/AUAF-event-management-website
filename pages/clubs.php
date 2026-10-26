<?php
$pageTitle = 'Student Clubs | AUAF';
require_once __DIR__ . '/../config/database.php';

try {
    $stmt = $pdo->query(
        'SELECT club_id, club_name, description, google_space_url
         FROM clubs
         ORDER BY club_name ASC'
    );

    $clubs = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('Unable to retrieve clubs: ' . $e->getMessage());
    $clubs = [];
    $clubsLoadError = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Discover student clubs and activities at AUAF.">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
          rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark site-navbar">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2"
           href="../index.php">
            <span class="brand-mark">A</span>
            <span class="brand-text">
                AUAF
                <small>EVENTS & ACTIVITIES</small>
            </span>
        </a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#mainNavigation"
                aria-controls="mainNavigation"
                aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavigation">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-3">
                <li class="nav-item">
                    <a class="nav-link" href="../index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="events.php">Events</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="clubs.php">Student Clubs</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../index.php#about">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="login.php">Sign In</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<section class="clubs-page-hero">
    <div class="container">
        <div class="clubs-breadcrumb">
            <a href="../index.php">Home</a>
            <i class="bi bi-chevron-right"></i>
            <span>Student Clubs</span>
        </div>

        <span class="section-eyebrow">FIND YOUR COMMUNITY</span>
        <h1>Discover Student <span>Clubs.</span></h1>
        <p>
            Meet students who share your interests, develop new skills,
            and get involved in the AUAF community.
        </p>
    </div>
</section>

<section class="clubs-browser">
    <div class="container">
        <div class="clubs-heading">
            <div>
                <span class="section-eyebrow">CONNECT AND PARTICIPATE</span>
                <h2>Explore Our Clubs</h2>
                <p>Find a community that matches your interests.</p>
            </div>

            <span class="club-count">
                <?= count($clubs) ?> clubs
            </span>
        </div>

        <div class="club-grid">
            <?php foreach ($clubs as $club): ?>
                <article class="club-card">
                    <div class="club-card-icon <?= htmlspecialchars($club['color']) ?>">
                        <i class="bi <?= htmlspecialchars($club['icon']) ?>"></i>
                    </div>

                    <span class="club-category">
                        <?= htmlspecialchars($club['category']) ?>
                    </span>

                    <h3><?= htmlspecialchars($club['name']) ?></h3>

                    <p><?= htmlspecialchars($club['description']) ?></p>

                    <a class="club-link"
                       href="club-details.php?club=<?= urlencode($club['name']) ?>">
                        Explore Club
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="clubs-note">
            <i class="bi bi-info-circle"></i>
            These are sample clubs for the frontend prototype.
            The database will supply the actual club information later.
        </div>
    </div>
</section>

<footer class="site-footer">
    <div class="container">
        <div class="row gy-4">
            <div class="col-lg-6">
                <a class="footer-brand" href="../index.php">AUAF EVENTS</a>
                <p class="footer-description">
                    Discover university events, student organizations,
                    and opportunities to connect with the AUAF community.
                </p>
            </div>

            <div class="col-12 col-lg-6 footer-explore">
                <h4>Explore</h4>
                <a href="../index.php">Home</a>
                <a href="events.php">Upcoming Events</a>
                <a href="clubs.php">Student Clubs</a>
            </div>
        </div>

        <div class="footer-bottom">
            <span>&copy; <?= date('Y') ?> AUAF Event Management Website</span>
            <span>Designed for the AUAF community</span>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
