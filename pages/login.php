<?php
session_start();

require_once __DIR__ . '/../config/database.php';

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    // Validate email and password
    if ($email === '') {

        $error = 'Please enter your email address.';

    } elseif ($password === '') {

        $error = 'Please enter your password.';

    } elseif (
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        !str_ends_with($email, '@auaf.edu.af') ||
        strlen($email) > 254
    ) {

        $error = 'Please enter a valid AUAF club email ending in @auaf.edu.af.';

    } else {

        try {

            // Find club account
            $stmt = $pdo->prepare(
                "SELECT club_id, club_name, email, password_hash
                 FROM clubs
                 WHERE LOWER(TRIM(email)) = :email
                 LIMIT 1"
            );

            $stmt->execute([
                'email' => $email
            ]);

            $club = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$club) {

                $error = 'No club account found with this email. Please register first.';

            } elseif (!password_verify($password, $club['password_hash'])) {

                $error = 'Incorrect password. Please try again.';

            } else {

                // Regenerate session ID after successful login
                session_regenerate_id(true);

                // Store club information in session
                $_SESSION['club_id'] = (int) $club['club_id'];
                $_SESSION['club_name'] = $club['club_name'];
                $_SESSION['club_email'] = $club['email'];

                // Redirect to dashboard
                header('Location: /club/dashboard.php');
                exit;
            }

        } catch (PDOException $e) {

            error_log('Club login error: ' . $e->getMessage());

            $error = 'Unable to process your login. Please try again.';
        }
    }
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

<body class="club-login-page">

<!-- Navigation -->
<nav class="site-navbar">
    <div class="container d-flex justify-content-between align-items-center flex-wrap gap-3">

        <a class="brand" href="/index.php">AUAF EVENTS</a>

        <div class="nav-links">
            <a href="/index.php">Home</a>
            <a href="/pages/events.php">Events</a>
            <a href="/pages/clubs.php">Student Clubs</a>
            <a href="/pages/about.php">About</a>
            <a href="/club/login.php" class="active">Club Sign In</a>
        </div>

    </div>
</nav>

<!-- Sign In Section -->
<main class="club-login-section">
    <div class="container">

        <div class="club-login-card">

            <div class="club-login-icon">
                <i class="bi bi-shield-lock"></i>
            </div>

            <span class="section-eyebrow">WELCOME BACK</span>

            <h1>Club Sign In</h1>

            <p class="club-login-subtitle">
                Sign in to manage your club and its events.
            </p>

            <?php if ($error !== ''): ?>
                <div class="alert alert-warning" role="alert">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form action="" method="post">

                <div class="mb-3 text-start">
                    <label for="club_email" class="form-label">
                        Official AUAF Club Email
                    </label>

                    <input
                        type="email"
                        class="form-control"
                        id="club_email"
                        name="email"
                        placeholder="clubname@auaf.edu.af"
                        value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
                        maxlength="254"
                        autocomplete="username"
                        required
                    >
                </div>

                <div class="mb-4 text-start">
                    <label for="password" class="form-label">
                        Password
                    </label>

                    <input
                        type="password"
                        class="form-control"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <button type="submit" class="login-submit-btn">
                    Sign In
                    <i class="bi bi-arrow-right"></i>
                </button>

            </form>

            <div class="club-login-divider">
                <span>NEW TO AUAF EVENTS?</span>
            </div>

            <p class="club-register-prompt">
                Register your organization to get started.
            </p>

            <a href="/club/register.php" class="register-club-btn">
                Register Your Club
            </a>

            <a href="/pages/clubs.php" class="back-to-clubs">
                <i class="bi bi-arrow-left"></i>
                Browse Student Clubs
            </a>

            <p class="login-demo-notice">
                <i class="bi bi-info-circle"></i>
                Account authentication will be enabled after database integration.
            </p>

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
