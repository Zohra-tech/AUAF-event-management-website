<?php
$pageTitle = "AUAF Event Management";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($pageTitle) ?></title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>


<nav class="site-navbar">
    <div class="container d-flex justify-content-between align-items-center flex-wrap gap-3">
        <a class="brand" href="index.php">AUAF EVENTS</a>

        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="pages/events.php">Events</a>
            <a href="pages/clubs.php">Clubs</a>
            <a href="pages/login.php">Sign In</a>
        </div>
    </div>
</nav>

<header class="hero">
    <img
        src="assets/images/auaf-header.png"
        alt="AUAF campus"
        class="hero-image"
    >

    <div class="hero-overlay"></div>

    <div class="container hero-content">
        <span class="hero-label">AMERICAN UNIVERSITY OF AFGHANISTAN</span>

        <h1>Discover What's Happening at AUAF</h1>

        <p>
            Explore university events, connect with student clubs,
            register for activities, and become part of the AUAF community.
        </p>

        <div class="hero-actions">
            <a href="pages/events.php" class="btn btn-gold">
                Explore Events
            </a>
        </div>
    </div>
</header>

<main class="container py-5">
    <div class="text-center mb-5">
        <h2>Everything Happening on Campus</h2>
        <p class="text-secondary">
            Find events, discover student organizations, and share feedback.
        </p>
    </div>

    <div class="row g-4">

        <div class="col-md-6 col-lg-4">
            <section class="feature-card">
                <h3>Upcoming Events</h3>
                <p>
                    Browse university events and find activities
                    that interest you.
                </p>
                <a href="pages/events.php">Browse events →</a>
            </section>
        </div>

        <div class="col-md-6 col-lg-4">
            <section class="feature-card">
                <h3>Student Clubs</h3>
                <p>
                    Explore student organizations and discover
                    opportunities to participate.
                </p>
                <a href="pages/clubs.php">Explore clubs →</a>
            </section>
        </div>

        <div class="col-md-6 col-lg-4">
            <section class="feature-card">
                <h3>Registration & Feedback</h3>
                <p>
                    Sign in to access available activities and
                    provide feedback on events.
                </p>
                <a href="pages/login.php">Sign in →</a>
            </section>
        </div>

    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

</body>
</html>
