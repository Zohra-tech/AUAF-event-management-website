<?php
$pageTitle = 'About Us | AUAF Event Management Website';
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

<body class="about-page">

<!-- Navigation -->
<nav class="site-navbar">
    <div class="container d-flex justify-content-between align-items-center flex-wrap gap-3">

        <a class="brand" href="/index.php">AUAF EVENTS</a>

        <div class="nav-links">
            <a href="/index.php">Home</a>
            <a href="/pages/events.php">Events</a>
            <a href="/pages/clubs.php">Student Clubs</a>
            <a href="/pages/about.php" class="active">About</a>
            <a href="/club/login.php">Club Sign In</a>
        </div>

    </div>
</nav>

<!-- Hero Section -->
<section class="about-hero">
    <div class="container">

        <div class="about-breadcrumb">
            <a href="/index.php">Home</a>
            <i class="bi bi-chevron-right"></i>
            <span>About Us</span>
        </div>

        <span class="section-eyebrow">ABOUT OUR PLATFORM</span>

        <h1>
            Bringing the AUAF
            Community Together.
        </h1>

        <p>
            Discover student organizations, explore virtual events,
            and connect with opportunities that make university life
            more engaging and collaborative.
        </p>

        <a href="/pages/events.php" class="about-hero-btn">
            Explore Events
            <i class="bi bi-arrow-right"></i>
        </a>

    </div>
</section>

<!-- About Introduction -->
<section class="about-intro-section">
    <div class="container">

        <div class="row align-items-center g-5">

            <div class="col-lg-6">

                <span class="about-eyebrow">WHO WE ARE</span>

                <h2>
                    A Central Place for
                    Student Engagement
                </h2>

                <p>
                    The AUAF Event Management Website is a centralized
                    platform designed to connect the American University
                    of Afghanistan community with student clubs and
                    university activities.
                </p>

                <p>
                    The platform allows student clubs to create accounts,
                    share their organizational information, publish
                    virtual events, and manage student registrations
                    independently.
                </p>

                <p>
                    Students can explore clubs, learn about their
                    activities, discover upcoming events, and register
                    online using their AUAF email addresses.
                </p>

            </div>

            <div class="col-lg-6">

                <div class="about-intro-visual">

                    <div class="about-visual-main">
                        <i class="bi bi-people-fill"></i>
                        <h3>Connect. Participate. Grow.</h3>
                        <p>
                            Building stronger connections across
                            the AUAF student community.
                        </p>
                    </div>

                    <div class="about-floating-card">
                        <i class="bi bi-calendar-event"></i>
                        <div>
                            <strong>Virtual Events</strong>
                            <span>Discover and participate online</span>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>
</section>

<!-- Mission and Vision -->
<section class="about-mission-section">
    <div class="container">

        <div class="about-section-heading text-center">
            <span class="about-eyebrow">OUR PURPOSE</span>
            <h2>Our Mission and Vision</h2>
            <p>
                Making student engagement more accessible,
                organized, and connected.
            </p>
        </div>

        <div class="row g-4">

            <div class="col-md-6">

                <div class="about-purpose-card">

                    <div class="about-purpose-icon">
                        <i class="bi bi-bullseye"></i>
                    </div>

                    <h3>Our Mission</h3>

                    <p>
                        To provide an accessible and organized platform
                        where student clubs can promote their activities
                        and students can easily discover and participate
                        in virtual university events.
                    </p>

                </div>

            </div>

            <div class="col-md-6">

                <div class="about-purpose-card">

                    <div class="about-purpose-icon">
                        <i class="bi bi-eye"></i>
                    </div>

                    <h3>Our Vision</h3>

                    <p>
                        To encourage a connected and active university
                        community where students can explore their
                        interests, collaborate with others, and take
                        part in meaningful extracurricular experiences.
                    </p>

                </div>

            </div>

        </div>

    </div>
</section>

<!-- Features -->
<section class="about-features-section">
    <div class="container">

        <div class="about-section-heading text-center">

            <span class="about-eyebrow">PLATFORM FEATURES</span>

            <h2>What You Can Do</h2>

            <p>
                Everything students and clubs need to discover
                and organize virtual university activities.
            </p>

        </div>

        <div class="row g-4">

            <div class="col-md-6 col-lg-4">
                <div class="about-feature-card">

                    <div class="about-feature-icon">
                        <i class="bi bi-people"></i>
                    </div>

                    <h3>Explore Student Clubs</h3>

                    <p>
                        Browse registered student organizations,
                        discover their goals, and connect through
                        their Google Space communities.
                    </p>

                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="about-feature-card">

                    <div class="about-feature-icon">
                        <i class="bi bi-calendar-event"></i>
                    </div>

                    <h3>Discover Virtual Events</h3>

                    <p>
                        Explore upcoming events, review their
                        details, and access virtual meeting links
                        from one central platform.
                    </p>

                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="about-feature-card">

                    <div class="about-feature-icon">
                        <i class="bi bi-pencil-square"></i>
                    </div>

                    <h3>Easy Event Registration</h3>

                    <p>
                        Students can register for events using
                        their names and official AUAF student
                        email addresses.
                    </p>

                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="about-feature-card">

                    <div class="about-feature-icon">
                        <i class="bi bi-person-workspace"></i>
                    </div>

                    <h3>Club Management</h3>

                    <p>
                        Club representatives can register,
                        sign in, and manage their own events
                        through a dedicated dashboard.
                    </p>

                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="about-feature-card">

                    <div class="about-feature-icon">
                        <i class="bi bi-link-45deg"></i>
                    </div>

                    <h3>Virtual Meeting Access</h3>

                    <p>
                        Each event can include a virtual meeting
                        URL, helping registered students access
                        online activities.
                    </p>

                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="about-feature-card">

                    <div class="about-feature-icon">
                        <i class="bi bi-bar-chart"></i>
                    </div>

                    <h3>Registration Tracking</h3>

                    <p>
                        Clubs can monitor event registrations
                        and review the students registered
                        for their activities.
                    </p>

                </div>
            </div>

        </div>

    </div>
</section>

<!-- How It Works -->
<section class="about-process-section">
    <div class="container">

        <div class="about-section-heading text-center">

            <span class="about-eyebrow">GETTING STARTED</span>

            <h2>How the Platform Works</h2>

            <p>
                A simple process for students and club representatives.
            </p>

        </div>

        <div class="row g-4">

            <div class="col-md-4">
                <div class="about-process-card">

                    <span class="process-number">01</span>

                    <i class="bi bi-search"></i>

                    <h3>Discover</h3>

                    <p>
                        Browse student clubs and explore
                        upcoming virtual events.
                    </p>

                </div>
            </div>

            <div class="col-md-4">
                <div class="about-process-card">

                    <span class="process-number">02</span>

                    <i class="bi bi-person-plus"></i>

                    <h3>Connect and Register</h3>

                    <p>
                        Connect with clubs through Google Space
                        and register for events using your AUAF email.
                    </p>

                </div>
            </div>

            <div class="col-md-4">
                <div class="about-process-card">

                    <span class="process-number">03</span>

                    <i class="bi bi-camera-video"></i>

                    <h3>Participate</h3>

                    <p>
                        Use the provided virtual event link
                        to access and participate in online activities.
                    </p>

                </div>
            </div>

        </div>

    </div>
</section>

<!-- Call to Action -->
<section class="about-cta-section">
    <div class="container text-center">

        <span class="about-eyebrow">BE PART OF THE COMMUNITY</span>

        <h2>
            Your Next Opportunity Starts Here.
        </h2>

        <p>
            Explore student clubs, discover upcoming events,
            and take the next step toward becoming more involved
            in the AUAF community.
        </p>

        <div class="about-cta-actions">

            <a href="/pages/clubs.php" class="about-cta-primary">
                Explore Student Clubs
                <i class="bi bi-arrow-right"></i>
            </a>

            <a href="/pages/events.php" class="about-cta-secondary">
                Browse Events
            </a>

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
                <a href="/pages/about.php">About Us</a>

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
