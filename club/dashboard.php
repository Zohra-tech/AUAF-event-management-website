<?php
session_start();

if (empty($_SESSION['club_id'])) {
    header('Location: /club/login.php');
    exit;
}

$clubName = $_SESSION['club_name'] ?? 'Your Student Club';

session_start();

if (empty($_SESSION['club_id'])) {
    header('Location: /club/login.php');
    exit;
}

$clubName = $_SESSION['club_name'] ?? 'Your Student Club';




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

<body class="club-dashboard-page">

<!-- Navigation -->
<nav class="site-navbar">
    <div class="container d-flex justify-content-between align-items-center flex-wrap gap-3">

        <a class="brand" href="/index.php">AUAF EVENTS</a>

        <div class="nav-links">
            <a href="/index.php">Home</a>
            <a href="/pages/events.php">Events</a>
            <a href="/pages/clubs.php">Student Clubs</a>
            <a href="/club/dashboard.php" class="active">Dashboard</a>
            <a href="/club/login.php">Sign In</a>
        </div>

    </div>
</nav>

<!-- Dashboard Header -->
<section class="club-dashboard-hero">
    <div class="container">

        <div class="dashboard-breadcrumb">
            <a href="/index.php">Home</a>
            <i class="bi bi-chevron-right"></i>
            <span>Club Dashboard</span>
        </div>

        <div class="dashboard-welcome">
            <div>
                <span class="section-eyebrow">CLUB MANAGEMENT PORTAL</span>

                <h1>Welcome to Your Dashboard</h1>

                <p>
                    Manage your club, organize events, and connect with
                    the AUAF student community.
                </p>
            </div>

            <div class="dashboard-welcome-icon">
                <i class="bi bi-buildings"></i>
            </div>
        </div>

    </div>
</section>

<!-- Dashboard Content -->
<main class="club-dashboard-content">
    <div class="container">

        <div class="dashboard-status-notice">
            <i class="bi bi-info-circle"></i>
            <div>
                <strong>Dashboard preview</strong>
                <p>
                    Your club's information and statistics is here!
                </p>
            </div>
        </div>

        <!-- Statistics -->
        <div class="dashboard-section-heading">
            <div>
                <span class="section-eyebrow">YOUR OVERVIEW</span>
                <h2>Club at a Glance</h2>
            </div>
        </div>

        <div class="dashboard-stat-grid">

            <article class="dashboard-stat-card">
                <div class="dashboard-stat-icon">
                    <i class="bi bi-calendar-event"></i>
                </div>

                <h3>Events Created</h3>
                <p class="stat-value">—</p>
            </article>

            <article class="dashboard-stat-card">
                <div class="dashboard-stat-icon">
                    <i class="bi bi-calendar-check"></i>
                </div>

                <h3>Upcoming Events</h3>
                <p class="stat-value">—</p>
            </article>

            <article class="dashboard-stat-card">
                <div class="dashboard-stat-icon">
                    <i class="bi bi-people"></i>
                </div>

                <h3>Student Registrations</h3>
                <p class="stat-value">—</p>
            </article>

        </div>

        <!-- Quick Actions -->
        <div class="dashboard-section-heading dashboard-actions-heading">
            <div>
                <span class="section-eyebrow">GET STARTED</span>
                <h2>Manage Your Club</h2>
                <p>Choose an action to continue.</p>
            </div>
        </div>

        <div class="dashboard-action-grid">

            <a href="/club/create-event.php" class="dashboard-action-card">
                <div class="dashboard-action-icon">
                    <i class="bi bi-plus-circle"></i>
                </div>

                <div>
                    <h3>Create an Event</h3>
                    <p>
                        Publish a new club event with its date, time,
                        location, and agenda.
                    </p>
                </div>

                <i class="bi bi-arrow-up-right action-arrow"></i>
            </a>

            <a href="/club/manage-events.php" class="dashboard-action-card">
                <div class="dashboard-action-icon">
                    <i class="bi bi-calendar2-week"></i>
                </div>

                <div>
                    <h3>Manage Events</h3>
                    <p>
                        Review your events and access event management tools.
                    </p>
                </div>

                <i class="bi bi-arrow-up-right action-arrow"></i>
            </a>

            <a href="/pages/clubs.php" class="dashboard-action-card">
                <div class="dashboard-action-icon">
                    <i class="bi bi-eye"></i>
                </div>

                <div>
                    <h3>View Public Clubs</h3>
                    <p>
                        See how student organizations are presented
                        on the public website.
                    </p>
                </div>

                <i class="bi bi-arrow-up-right action-arrow"></i>
            </a>

        </div>

        <!-- Upcoming Events Preview -->
        <section class="dashboard-events-panel">

            <div class="dashboard-events-heading">
                <div>
                    <span class="section-eyebrow">YOUR SCHEDULE</span>
                    <h2>Upcoming Events</h2>
                </div>

                <a href="/club/manage-events.php" class="dashboard-view-all">
                    Manage Events
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="dashboard-empty-state">
                <div class="dashboard-empty-icon">
                    <i class="bi bi-calendar2-x"></i>
                </div>

                <h3>No event information available yet</h3>

                <p>
                    Your club's upcoming events will appear here after
                    event management is connected to the database.
                </p>

                <a href="/club/create-event.php" class="dashboard-primary-btn">
                    <i class="bi bi-plus-lg"></i>
                    Create an Event
                </a>
            </div>

        </section>

    </div>
</main>

<!-- Footer -->
<footer class="site-footer">
    <div class="container">

        <div class="row gy-4">

            <div class="col-lg-6">
                <a class="footer-brand" href="/index.php">AUAF EVENTS</a>

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
            <span>&copy; <?= date('Y') ?> AUAF Event Management Website</span>
            <span>Designed for the AUAF community</span>
        </div>

    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
