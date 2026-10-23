<?php
session_start();
$pageTitle = "AUAF Events | American University of Afghanistan";

include 'db_connect.php';

$sql = "SELECT * FROM events ORDER BY date ASC";
$result = $conn->query($sql);

$events = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Discover campus events, student clubs, and activities at the American University of Afghanistan.">
    <title><?= htmlspecialchars($pageTitle) ?></title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
          rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark site-navbar">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
            <span class="brand-mark">A</span>
            <span class="brand-text">
                AUAF
                <small>EVENTS & ACTIVITIES</small>
            </span>
        </a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#mainNavigation"
                aria-controls="mainNavigation" aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavigation">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-3">
                <li class="nav-item">
                    <a class="nav-link active" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pages/events.php">Events</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pages/clubs.php">Student Clubs</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#about">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pages/login.php">Sign In</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-gold" href="pages/events.php">
                        Explore Events <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- AUAF Hero Banner -->
<header class="hero">
    <img
        src="assets/images/auaf-header.png"
        alt="Campus of the American University of Afghanistan"
        class="hero-image"
    >

    <div class="hero-overlay"></div>

    <div class="container hero-content">
        <div class="hero-label">
            <span class="hero-label-dot"></span>
            AMERICAN UNIVERSITY OF AFGHANISTAN
        </div>

        <h1>Discover. Connect.<br><span>Be Part of Campus Life.</span></h1>

        <p class="hero-description">
            Your gateway to university events, student clubs, inspiring ideas,
            and experiences that bring the AUAF community together.
        </p>

        <div class="hero-actions">
            <a href="pages/events.php" class="btn btn-gold btn-lg">
                Explore Upcoming Events
                <i class="bi bi-arrow-up-right ms-2"></i>
            </a>
            <a href="#about" class="btn btn-outline-light btn-lg">
                Discover AUAF Events
            </a>
        </div>

        <div class="hero-note">
            <i class="bi bi-calendar-event"></i>
            One community. Many opportunities.
        </div>
    </div>

    <div class="hero-bottom-line"></div>
</header>

<!-- Welcome Section -->
<section class="welcome-section" id="about">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <span class="section-eyebrow">CAMPUS LIFE STARTS HERE</span>
                <h2 class="section-title">
                    Make Every University<br>
                    <span>Experience Count.</span>
                </h2>
                <p class="section-description">
                    From academic discussions and cultural gatherings to
                    student-led initiatives, discover opportunities to learn,
                    collaborate, and make meaningful connections across the AUAF
                    community.
                </p>
            </div>

            <div class="col-lg-5">
                <div class="welcome-highlight">
                    <div class="highlight-icon">
                        <i class="bi bi-people"></i>
                    </div>
                    <div>
                        <h3>A Community of Opportunities</h3>
                        <p>
                            Find events hosted by university management and
                            student clubs, all in one place.
                        </p>
                        <a href="pages/clubs.php">
                            Discover student clubs
                            <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Upcoming Events -->
<section class="events-section" id="events">
    <div class="container">
        <div class="section-heading-row">
            <div>
                <span class="section-eyebrow">SAVE YOUR SPOT</span>
                <h2 class="section-title">Upcoming Events</h2>
                <p class="section-description">
                    Find your next opportunity to learn, connect, and participate.
                </p>
            </div>

            <a href="pages/events.php" class="view-all-link">
                View all events <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="row g-4">
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

                            <a href="pages/events.php" class="event-link">
                                Event details
                                <i class="bi bi-arrow-up-right"></i>
                            </a>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<!-- Admin Access Section for managing events -->
<?php if (!isset($_SESSION['username'])): ?>
    <div class="mt-4 text-center">
        <a href="login.php" class="btn btn-dark btn-lg">
            🔐 Login to Manage Events
        </a>
    </div>
<?php else: ?>
    <?php if ($_SESSION['role'] === 'admin'): ?>
        <div class="mt-4 text-center">
            <a href="pages/events.php" class="btn btn-gold btn-lg">
                ⚙️ Manage Events
            </a>
        </div>
    <?php endif; ?>
<?php endif; ?>


<!-- Call to Action -->
<section class="community-section">
    <div class="container">
        <div class="community-content">
            <span class="section-eyebrow">YOUR CAMPUS. YOUR COMMUNITY.</span>
            <h2>There's a Place for You at AUAF.</h2>
            <p>
                Explore student clubs, participate in university activities,
                and make your time at AUAF more meaningful.
            </p>
            <a href="pages/clubs.php" class="btn btn-gold btn-lg">
                Explore Student Clubs
                <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="site-footer">
    <div class="container">
        <div class="row gy-4">
            <div class="col-lg-6">
                <a class="footer-brand" href="index.php">AUAF EVENTS</a>
                <p class="footer-description">
                    A central place to discover university events, student
                    organizations, and campus opportunities.
                </p>
            </div>

            <div class="col-6 col-lg-3">
                <h4>Explore</h4>
                <a href="pages/events.php">Upcoming Events</a>
                <a href="pages/clubs.php">Student Clubs</a>
                <a href="#about">About</a>
            </div>

            <div class="col-6 col-lg-3">
                <h4>Get Involved</h4>
                <a href="pages/login.php">Sign In</a>
                <a href="pages/register.php">Create Account</a>
                <a href="pages/feedback.php">Feedback</a>
            </div>
        </div>

        <div class="footer-bottom">
            <span>&copy; <?= date('Y') ?> AUAF Event Management Website</span>
            <span>Designed for the AUAF community</span>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>
