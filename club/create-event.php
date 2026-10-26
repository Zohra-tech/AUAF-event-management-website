
<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (empty($_SESSION['club_id'])) {
    header('Location: /club/login.php');
    exit;
}

$errors = [];
$success = '';

$eventTitle = '';
$description = '';
$eventDate = '';
$eventTime = '';
$location = '';
$agenda = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventTitle = trim($_POST['event_title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $eventDate = trim($_POST['event_date'] ?? '');
    $eventTime = trim($_POST['event_time'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $agenda = trim($_POST['agenda'] ?? '');

    if ($eventTitle === '') {
        $errors[] = 'Event title is required.';
    }

    if ($description === '') {
        $errors[] = 'Event description is required.';
    }

    $date = DateTimeImmutable::createFromFormat('!Y-m-d', $eventDate);
    if (!$date || $date->format('Y-m-d') !== $eventDate ||
        $eventDate < date('Y-m-d')) {
        $errors[] = 'Please select a valid date that is not in the past.';
    }

    $time = DateTimeImmutable::createFromFormat('!H:i', $eventTime);
    if (!$time || $time->format('H:i') !== $eventTime) {
        $errors[] = 'Please select a valid event time.';
    }

    if ($location === '') {
        $errors[] = 'Event location is required.';
    }

    if (!$errors) {
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO events
                (club_id, event_title, description, event_date,
                 event_time, location, agenda, status)
                VALUES
                (:club_id, :event_title, :description, :event_date,
                 :event_time, :location, :agenda, :status)'
            );

            $stmt->execute([
                'club_id' => (int) $_SESSION['club_id'],
                'event_title' => $eventTitle,
                'description' => $description,
                'event_date' => $eventDate,
                'event_time' => $eventTime,
                'location' => $location,
                'agenda' => $agenda !== '' ? $agenda : null,
                'status' => 'upcoming',
            ]);

            header('Location: /club/manage-events.php?created=1');
            exit;
        } catch (PDOException $e) {
            error_log('Event creation failed: ' . $e->getMessage());
            $errors[] = 'Unable to save the event. Please contact the website administrator.';
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
                            <label for="event_link" class="form-label">
                                Event Link <span class="required-star">*</span>
                            </label>
                            <input
                                type="url"
                                class="form-control"
                                id="event_link"
                                name="event_link"
                                maxlength="255"
                                placeholder="e.g. https://example.com/event"
                                value="<?= htmlspecialchars($eventLink, ENT_QUOTES, 'UTF-8') ?>"
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
