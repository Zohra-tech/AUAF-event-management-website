<?php
$pageTitle = 'Register Your Club | AUAF Events';

$errors = [];
$clubName = '';
$clubEmail = '';
$clubDescription = '';
$googleSpaceUrl = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clubName = trim($_POST['club_name'] ?? '');
    $clubEmail = strtolower(trim($_POST['club_email'] ?? ''));
    $clubDescription = trim($_POST['description'] ?? '');
    $googleSpaceUrl = trim($_POST['google_space_url'] ?? '');

    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($clubName === '') {
        $errors[] = 'Please enter your official club name.';
    }

    if (!filter_var($clubEmail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid club email address.';
    } elseif (!str_ends_with($clubEmail, '@auaf.edu.af')) {
        $errors[] = 'Please use your official AUAF email ending in @auaf.edu.af.';
    }

    if ($clubDescription === '') {
        $errors[] = 'Please provide a description of your club.';
    }

    if ($googleSpaceUrl !== '') {
        $urlParts = parse_url($googleSpaceUrl);

        if (
            !filter_var($googleSpaceUrl, FILTER_VALIDATE_URL) ||
            !in_array(strtolower($urlParts['scheme'] ?? ''), ['http', 'https'], true)
        ) {
            $errors[] = 'Please enter a valid Google Space URL.';
        }
    }

    if (strlen($password) < 8) {
        $errors[] = 'Your password must be at least 8 characters long.';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'Your passwords do not match.';
    }

    // Database saving will be added after the database schema is available.
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

<body class="club-register-page">

<!-- Navigation -->
<nav class="site-navbar">
    <div class="container d-flex justify-content-between align-items-center flex-wrap gap-3">

        <a class="brand" href="/index.php">AUAF EVENTS</a>

        <div class="nav-links">
            <a href="/index.php">Home</a>
            <a href="/pages/events.php">Events</a>
            <a href="/pages/clubs.php">Student Clubs</a>
            <a href="/pages/about.php">About</a>
            <a href="/club/login.php">Club Sign In</a>
        </div>

    </div>
</nav>

<!-- Registration Section -->
<main class="club-register-section">
    <div class="container">

        <div class="club-register-wrapper">

            <div class="club-register-intro">
                <span class="section-eyebrow">JOIN THE AUAF COMMUNITY</span>

                <h1>Register Your Club</h1>

                <p>
                    Create your club account to share your organization's
                    information, manage events, and connect with AUAF students.
                </p>

                <div class="register-benefit">
                    <i class="bi bi-calendar-event"></i>
                    <div>
                        <h3>Manage Events</h3>
                        <p>Create and manage your club's upcoming events.</p>
                    </div>
                </div>

                <div class="register-benefit">
                    <i class="bi bi-people"></i>
                    <div>
                        <h3>Reach Students</h3>
                        <p>Help students discover your club and activities.</p>
                    </div>
                </div>

                <div class="register-benefit">
                    <i class="bi bi-graph-up-arrow"></i>
                    <div>
                        <h3>Track Participation</h3>
                        <p>View student registrations for your events.</p>
                    </div>
                </div>
            </div>

            <div class="club-register-form-card">

                <div class="register-form-heading">
                    <h2>Create a Club Account</h2>
                    <p>Complete the form below to get started.</p>
                </div>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger" role="alert">
                        <strong>Please check the following:</strong>
                        <ul class="mb-0 mt-2">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="" method="post">

                    <div class="mb-3">
                        <label for="club_name" class="form-label">
                            Official Club Name
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="club_name"
                            name="club_name"
                            placeholder="e.g. AUAF Technology Club"
                            value="<?= htmlspecialchars($clubName, ENT_QUOTES, 'UTF-8') ?>"
                            maxlength="150"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label for="club_email" class="form-label">
                            Official AUAF Club Email
                        </label>

                        <input
                            type="email"
                            class="form-control"
                            id="club_email"
                            name="club_email"
                            placeholder="clubname@auaf.edu.af"
                            value="<?= htmlspecialchars($clubEmail, ENT_QUOTES, 'UTF-8') ?>"
                            maxlength="254"
                            required
                        >

                        <div class="form-text">
                            Use an email address ending in @auaf.edu.af.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">
                            Club Description
                        </label>

                        <textarea
                            class="form-control"
                            id="description"
                            name="description"
                            rows="4"
                            placeholder="Tell students about your club, its goals, and activities."
                            required
                        ><?= htmlspecialchars($clubDescription, ENT_QUOTES, 'UTF-8') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="google_space_url" class="form-label">
                            Google Space URL
                            <span class="optional-label">(Optional)</span>
                        </label>

                        <input
                            type="url"
                            class="form-control"
                            id="google_space_url"
                            name="google_space_url"
                            placeholder="https://..."
                            value="<?= htmlspecialchars($googleSpaceUrl, ENT_QUOTES, 'UTF-8') ?>"
                        >
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            Create Password
                        </label>

                        <input
                            type="password"
                            class="form-control"
                            id="password"
                            name="password"
                            placeholder="At least 8 characters"
                            minlength="8"
                            autocomplete="new-password"
                            required
                        >
                    </div>

                    <div class="mb-4">
                        <label for="confirm_password" class="form-label">
                            Confirm Password
                        </label>

                        <input
                            type="password"
                            class="form-control"
                            id="confirm_password"
                            name="confirm_password"
                            placeholder="Enter your password again"
                            minlength="8"
                            autocomplete="new-password"
                            required
                        >
                    </div>

                    <button type="submit" class="register-submit-btn">
                        Create Club Account
                        <i class="bi bi-arrow-right"></i>
                    </button>

                    <p class="register-login-text">
                        Already registered?
                        <a href="/club/login.php">Sign in here</a>
                    </p>

                    <p class="register-demo-notice">
                        <i class="bi bi-info-circle"></i>
                        Registration is currently in frontend testing mode.
                        Your information will not be saved yet.
                    </p>

                </form>

            </div>
        </div>

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
