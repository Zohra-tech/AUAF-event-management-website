<?php
session_start();

require_once __DIR__ . '/../config/database.php';

$eventId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$eventId || $eventId < 1) {
    header('Location: /pages/events.php');
    exit;
}

$event = null;
$errors = [];

$firstName = '';
$lastName = '';
$studentEmail = '';

$registrationSuccess = isset($_GET['registered']) &&
    $_GET['registered'] === '1';

try {
    // Load the selected event and its club.
    $stmt = $pdo->prepare(
        "SELECT
            e.event_id,
            e.event_title,
            e.description,
            e.event_date,
            e.event_time,
            e.event_url,
            e.agenda,
            c.club_id,
            c.club_name
         FROM events e
         INNER JOIN clubs c ON c.club_id = e.club_id
         WHERE e.event_id = :event_id
           AND e.status = 'upcoming'
           AND e.event_date >= CURDATE()
         LIMIT 1"
    );

    $stmt->execute(['event_id' => $eventId]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        http_response_code(404);
    }

    // Generate CSRF token.
    if (empty($_SESSION['event_registration_csrf'])) {
        $_SESSION['event_registration_csrf'] = bin2hex(random_bytes(32));
    }

    // Process student registration.
    if (
        $event &&
        $_SERVER['REQUEST_METHOD'] === 'POST' &&
        !$registrationSuccess
    ) {
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $studentEmail = strtolower(trim($_POST['student_email'] ?? ''));

        $submittedToken = $_POST['csrf_token'] ?? '';
        $sessionToken = $_SESSION['event_registration_csrf'] ?? '';

        // Verify CSRF token.
        if (
            !is_string($submittedToken) ||
            !is_string($sessionToken) ||
            $sessionToken === '' ||
            !hash_equals($sessionToken, $submittedToken)
        ) {
            $errors[] = 'Your form session has expired. Please refresh the page and try again.';
        }

        // Validate first name.
        if ($firstName === '') {
            $errors[] = 'Please enter your first name.';
        } elseif (mb_strlen($firstName) > 100) {
            $errors[] = 'Your first name must not exceed 100 characters.';
        }

        // Validate last name.
        if ($lastName === '') {
            $errors[] = 'Please enter your last name.';
        } elseif (mb_strlen($lastName) > 100) {
            $errors[] = 'Your last name must not exceed 100 characters.';
        }

        // Validate AUAF student email.
        if (
            !filter_var($studentEmail, FILTER_VALIDATE_EMAIL) ||
            !str_ends_with($studentEmail, '@auaf.edu.af') ||
            strlen($studentEmail) > 254
        ) {
            $errors[] = 'Please enter a valid AUAF student email ending in @auaf.edu.af.';
        }

        // Insert registration if validation passes.
        if (empty($errors)) {
            try {
                $stmt = $pdo->prepare(
                    "INSERT INTO event_registrations
                        (event_id, first_name, last_name, student_email, status)
                     VALUES
                        (:event_id, :first_name, :last_name, :student_email, 'registered')"
                );

                $stmt->execute([
                    'event_id' => $eventId,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'student_email' => $studentEmail
                ]);

                // Prevent accidental form resubmission.
                unset($_SESSION['event_registration_csrf']);

                header(
                    'Location: /pages/register-event.php?id=' .
                    $eventId . '&registered=1'
                );
                exit;

            } catch (PDOException $e) {
                if ($e->getCode() === '23000') {
                    $errors[] = 'This email address is already registered for this event.';
                } else {
                    error_log('Event registration failed: ' . $e->getMessage());
                    $errors[] = 'We could not complete your registration. Please try again later.';
                }
            }
        }
    }

} catch (PDOException $e) {
    error_log('Unable to load event registration page: ' . $e->getMessage());
    http_response_code(500);
    exit('Unable to load this event. Please try again later.');
}

function escapeRegistration($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Register for Event | AUAF Events</title>

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
            data-bs-target="#registrationNavbar"
            aria-controls="registrationNavbar"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="registrationNavbar">
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/index.php">Home</a>
                <a class="nav-link active" href="/pages/events.php">Upcoming Events</a>
                <a class="nav-link" href="/pages/clubs.php">Student Clubs</a>
                <a class="nav-link" href="/pages/about.php">About</a>
            </div>
        </div>

    </div>
</nav>

<?php if (!$event): ?>

    <main class="container py-5">
        <div class="registration-message text-center p-5 rounded-4">
            <i class="bi bi-calendar-x display-3"></i>

            <h1 class="h3 fw-bold mt-3">Event Unavailable</h1>

            <p class="text-muted">
                This event could not be found, has been cancelled,
                or is no longer accepting registrations.
            </p>

            <a href="/pages/events.php" class="btn btn-warning fw-semibold">
                Browse Events
            </a>
        </div>
    </main>

<?php else: ?>

    <!-- Page Header -->
    <header class="registration-hero">
        <div class="container py-5">

            <a
                href="/pages/club-details.php?id=<?= (int) $event['club_id'] ?>"
                class="registration-back-link"
            >
                <i class="bi bi-arrow-left me-2"></i>
                View Club
            </a>

            <p class="registration-eyebrow mt-4">
                AUAF STUDENT COMMUNITY
            </p>

            <h1 class="display-5 fw-bold text-white">
                Register for an Event
            </h1>

            <p class="registration-hero-text">
                Complete the form below to register for your selected event.
            </p>

        </div>
    </header>

    <main class="container py-5">

        <div class="row g-4 justify-content-center">

            <!-- Event Information -->
            <div class="col-lg-5">

                <section class="registration-event-card card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4 p-lg-5">

                        <p class="registration-eyebrow">EVENT DETAILS</p>

                        <h2 class="h3 fw-bold mb-3">
                            <?= escapeRegistration($event['event_title']) ?>
                        </h2>

                        <p class="text-muted">
                            <?= nl2br(escapeRegistration($event['description'])) ?>
                        </p>

                        <hr>

                        <p>
                            <i class="bi bi-people me-2"></i>
                            <strong>Organized by:</strong><br>
                            <span class="ms-4">
                                <?= escapeRegistration($event['club_name']) ?>
                            </span>
                        </p>

                        <p>
                            <i class="bi bi-calendar-event me-2"></i>
                            <strong>Date:</strong><br>
                            <span class="ms-4">
                                <?= date('F j, Y', strtotime($event['event_date'])) ?>
                            </span>
                        </p>

                        <p>
                            <i class="bi bi-clock me-2"></i>
                            <strong>Time:</strong><br>
                            <span class="ms-4">
                                <?= date('g:i A', strtotime($event['event_time'])) ?>
                            </span>
                        </p>

                        <p class="mb-0">
                            <i class="bi bi-link-45deg me-2"></i>
                            <strong>Event URL:</strong><br>
                            <span class="ms-4">
                                <a
                                    href="<?= escapeRegistration($event['event_url']) ?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    <?= escapeRegistration($event['event_url']) ?>
                                </a>
                            </span>
                        </p>

                    </div>
                </section>

            </div>

            <!-- Registration Form -->
            <div class="col-lg-7">

                <section class="registration-form-card card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 p-lg-5">

                        <p class="registration-eyebrow">STUDENT REGISTRATION</p>

                        <h2 class="h3 fw-bold mb-2">
                            Your Information
                        </h2>

                        <p class="text-muted mb-4">
                            Use your official AUAF student email address.
                        </p>

                        <?php if ($registrationSuccess): ?>

                            <div class="alert alert-success" role="status">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                <strong>Registration successful!</strong>
                                You have been registered for this event.
                            </div>

                            <a
                                href="/pages/events.php"
                                class="btn btn-warning fw-semibold"
                            >
                                Browse More Events
                            </a>

                            <a
                                href="/pages/club-details.php?id=<?= (int) $event['club_id'] ?>"
                                class="btn btn-outline-secondary ms-2"
                            >
                                View Club
                            </a>

                        <?php else: ?>

                            <?php if (!empty($errors)): ?>
                                <div class="alert alert-danger" role="alert">
                                    <p class="fw-bold mb-2">
                                        Please correct the following:
                                    </p>

                                    <ul class="mb-0">
                                        <?php foreach ($errors as $error): ?>
                                            <li><?= escapeRegistration($error) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <form
                                method="POST"
                                action="/pages/register-event.php?id=<?= (int) $eventId ?>"
                            >

                                <input
                                    type="hidden"
                                    name="csrf_token"
                                    value="<?= escapeRegistration($_SESSION['event_registration_csrf'] ?? '') ?>"
                                >

                                <div class="row g-3">

                                    <div class="col-md-6">
                                        <label for="first_name" class="form-label fw-semibold">
                                            First Name
                                        </label>

                                        <input
                                            type="text"
                                            class="form-control form-control-lg"
                                            id="first_name"
                                            name="first_name"
                                            maxlength="100"
                                            autocomplete="given-name"
                                            value="<?= escapeRegistration($firstName) ?>"
                                            required
                                        >
                                    </div>

                                    <div class="col-md-6">
                                        <label for="last_name" class="form-label fw-semibold">
                                            Last Name
                                        </label>

                                        <input
                                            type="text"
                                            class="form-control form-control-lg"
                                            id="last_name"
                                            name="last_name"
                                            maxlength="100"
                                            autocomplete="family-name"
                                            value="<?= escapeRegistration($lastName) ?>"
                                            required
                                        >
                                    </div>

                                    <div class="col-12">
                                        <label for="student_email" class="form-label fw-semibold">
                                            AUAF Student Email
                                        </label>

                                        <input
                                            type="email"
                                            class="form-control form-control-lg"
                                            id="student_email"
                                            name="student_email"
                                            maxlength="254"
                                            placeholder="yourname@auaf.edu.af"
                                            autocomplete="email"
                                            value="<?= escapeRegistration($studentEmail) ?>"
                                            required
                                        >

                                        <div class="form-text">
                                            Your email must end with @auaf.edu.af.
                                        </div>
                                    </div>

                                    <div class="col-12 mt-4">
                                        <button
                                            type="submit"
                                            class="btn btn-warning btn-lg fw-semibold w-100 registration-submit-btn"
                                        >
                                            <i class="bi bi-check-circle me-2"></i>
                                            Confirm Registration
                                        </button>
                                    </div>

                                </div>

                            </form>

                        <?php endif; ?>

                        <p class="small text-muted mt-4 mb-0">
                            Your registration is associated with this event
                            and your AUAF student email address.
                        </p>

                    </div>
                </section>

            </div>

        </div>

        <div class="text-center mt-4">
            <a href="/pages/events.php" class="registration-events-link">
                <i class="bi bi-arrow-left me-2"></i>
                Return to All Events
            </a>
        </div>

    </main>

<?php endif; ?>

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