<?php
$pageTitle = 'Create Event | AUAF Events';

$errors = [];
$success = false;

$eventTitle = '';
$eventDescription = '';
$eventDate = '';
$eventTime = '';
$eventLocation = '';
$eventAgenda = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventTitle = trim($_POST['event_title'] ?? '');
    $eventDescription = trim($_POST['event_description'] ?? '');
    $eventDate = trim($_POST['event_date'] ?? '');
    $eventTime = trim($_POST['event_time'] ?? '');
    $eventLocation = trim($_POST['event_location'] ?? '');
    $eventAgenda = trim($_POST['event_agenda'] ?? '');

    if ($eventTitle === '') {
        $errors[] = 'Please enter an event title.';
    }

    if ($eventDescription === '') {
        $errors[] = 'Please provide an event description.';
    }

    $dateObject = DateTime::createFromFormat('!Y-m-d', $eventDate);
    if (!$dateObject || $dateObject->format('Y-m-d') !== $eventDate) {
        $errors[] = 'Please select a valid event date.';
    } elseif ($eventDate < date('Y-m-d')) {
        $errors[] = 'The event date cannot be in the past.';
    }

    $timeObject = DateTime::createFromFormat('!H:i', $eventTime);
    if (!$timeObject || $timeObject->format('H:i') !== $eventTime) {
        $errors[] = 'Please select a valid event time.';
    }

    if ($eventLocation === '') {
        $errors[] = 'Please enter the event location.';
    }

    if ($eventAgenda === '') {
        $errors[] = 'Please provide the event agenda.';
    }

    // Saving the event will be implemented after the database schema
    // and club authentication are available.
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

<body class="create-event-page">

<!-- Navigation -->
<nav class="site-navbar">
    <div class="container d-flex justify-content-between align-items-center flex-wrap gap-3">
        <a class="brand" href="/index.php">AUAF EVENTS</a>

        <div class="nav-links">
            <a href="/index.php">Home</a>
            <a href="/pages/events.php">Events</a>
            <a href="/pages/clubs.php">Student Clubs</a>
            <a href="/club/dashboard.php">Dashboard</a>
            <a href="/club/login.php">Sign In</a>
        </div>
    </div>
</nav>

<!-- Page Header -->
<section class="create-event-hero">
    <div class="container">
        <div class="create-event-breadcrumb">
            <a href="/index.php">Home</a>
            <i class="bi bi-chevron-right"></i>
            <a href="/club/dashboard.php">Dashboard</a>
            <i class="bi bi-chevron-right"></i>
            <span>Create Event</span>
        </div>

        <span class="section-eyebrow">CLUB EVENT MANAGEMENT</span>
        <h1>Create a New Event</h1>
        <p>
            Share your next activity with the AUAF community.
            Enter the details below to prepare your event.
        </p>
    </div>
</section>

<!-- Form -->
<main class="create-event-section">
    <div class="container">
        <div class="create-event-layout">

            <div class="create-event-main">
                <div class="create-event-card">

                    <div class="create-event-card-heading">
                        <div class="create-event-heading-icon">
                            <i class="bi bi-calendar-plus"></i>
                        </div>
                        <div>
                            <h2>Event Information</h2>
                            <p>Complete all required fields.</p>
                        </div>
                    </div>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger" role="alert">
                            <strong>Please correct the following:</strong>
                            <ul class="mb-0 mt-2">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="" method="post">

                        <div class="mb-4">
                            <label for="event_title" class="form-label">
                                Event Title <span class="required-star">*</span>
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                id="event_title"
                                name="event_title"
                                maxlength="200"
                                placeholder="e.g. Technology and Innovation Workshop"
                                value="<?= htmlspecialchars($eventTitle, ENT_QUOTES, 'UTF-8') ?>"
                                required
                            >
                        </div>

                        <div class="mb-4">
                            <label for="event_description" class="form-label">
                                Event Description <span class="required-star">*</span>
                            </label>
                            <textarea
                                class="form-control"
                                id="event_description"
                                name="event_description"
                                rows="4"
                                placeholder="Describe the purpose of your event and what participants can expect."
                                required
                            ><?= htmlspecialchars($eventDescription, ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="event_date" class="form-label">
                                    Event Date <span class="required-star">*</span>
                                </label>
                                <input
                                    type="date"
                                    class="form-control"
                                    id="event_date"
                                    name="event_date"
                                    min="<?= date('Y-m-d') ?>"
                                    value="<?= htmlspecialchars($eventDate, ENT_QUOTES, 'UTF-8') ?>"
                                    required
                                >
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="event_time" class="form-label">
                                    Event Time <span class="required-star">*</span>
                                </label>
                                <input
                                    type="time"
                                    class="form-control"
                                    id="event_time"
                                    name="event_time"
                                    value="<?= htmlspecialchars($eventTime, ENT_QUOTES, 'UTF-8') ?>"
                                    required
                                >
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="event_location" class="form-label">
                                Event Location <span class="required-star">*</span>
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                id="event_location"
                                name="event_location"
                                maxlength="255"
                                placeholder="e.g. AUAF Main Campus, Auditorium"
                                value="<?= htmlspecialchars($eventLocation, ENT_QUOTES, 'UTF-8') ?>"
                                required
                            >
                        </div>

                        <div class="mb-4">
                            <label for="event_agenda" class="form-label">
                                Event Agenda <span class="required-star">*</span>
                            </label>
                            <textarea
                                class="form-control"
                                id="event_agenda"
                                name="event_agenda"
                                rows="5"
                                placeholder="Outline the topics, activities, or schedule for your event."
                                required
                            ><?= htmlspecialchars($eventAgenda, ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>

                        <div class="create-event-form-actions">
                            <a href="/club/dashboard.php" class="event-cancel-btn">
                                Cancel
                            </a>

                            <button type="submit" class="event-submit-btn">
                                <i class="bi bi-plus-circle"></i>
                                Prepare Event
                            </button>
                        </div>

                        <p class="event-form-notice">
                            <i class="bi bi-info-circle"></i>
                            This form is in preview mode. Events will not be saved
                            or published until database integration is complete.
                        </p>

                    </form>
                </div>
            </div>

            <!-- Sidebar -->
            <aside class="create-event-sidebar">
                <div class="event-guidelines-card">
                    <div class="guidelines-icon">
                        <i class="bi bi-lightbulb"></i>
                    </div>

                    <h3>Event Planning Tips</h3>

                    <ul>
                        <li>Choose a clear, descriptive event title.</li>
                        <li>Explain who should attend and what they will learn.</li>
                        <li>Double-check your date, time, and venue.</li>
                        <li>Provide an agenda so students know what to expect.</li>
                    </ul>
                </div>

                <div class="event-guidelines-card event-preview-note">
                    <i class="bi bi-shield-check"></i>
                    <h3>Before Publishing</h3>
                    <p>
                        Once connected, your club will be able to save and
                        manage its own events. Authentication will ensure
                        clubs can only manage their own records.
                    </p>
                </div>
            </aside>

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
