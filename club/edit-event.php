<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (empty($_SESSION['club_id'])) {
    header('Location: /club/login.php');
    exit;
}

$clubId = (int) $_SESSION['club_id'];
$eventId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$eventId) {
    header('Location: /club/manage-events.php');
    exit;
}

$stmt = $pdo->prepare(
    'SELECT event_id, event_title, description, event_date,
            event_time, event_url, agenda
     FROM events
     WHERE event_id = :event_id AND club_id = :club_id
       AND status = \'upcoming\''
);
$stmt->execute(['event_id' => $eventId, 'club_id' => $clubId]);
$event = $stmt->fetch();

if (!$event) {
    http_response_code(404);
    exit('Event not found, cancelled, or not owned by your club.');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['event_title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $date = trim($_POST['event_date'] ?? '');
    $time = trim($_POST['event_time'] ?? '');
    $eventurl = trim($_POST['event_url'] ?? '');
    $agenda = trim($_POST['agenda'] ?? '');

    $parsedDate = DateTimeImmutable::createFromFormat('!Y-m-d', $date);
    $parsedTime = DateTimeImmutable::createFromFormat('!H:i', $time);

    if ($title === '' || $description === '' || $eventurl === '') {
        $errors[] = 'Title, description, and event URL are required.';
    }

    if (!$parsedDate || $parsedDate->format('Y-m-d') !== $date ||
        $date < date('Y-m-d')) {
        $errors[] = 'Choose a valid date that is not in the past.';
    }

    if (!$parsedTime || $parsedTime->format('H:i') !== $time) {
        $errors[] = 'Choose a valid event time.';
    }

    if (!$errors) {
        $update = $pdo->prepare(
            'UPDATE events
             SET event_title = :event_title,
                 description = :description,
                 event_date = :event_date,
                 event_time = :event_time,
                 event_url = :event_url,
                 agenda = :agenda
             WHERE event_id = :event_id
               AND club_id = :club_id
               AND status = \'upcoming\''
        );

        $update->execute([
            'event_title' => $title,
            'description' => $description,
            'event_date' => $date,
            'event_time' => $time,
            'event_url' => $eventurl,
            'agenda' => $agenda !== '' ? $agenda : null,
            'event_id' => $eventId,
            'club_id' => $clubId,
        ]);

        header('Location: /club/manage-events.php?updated=1');
        exit;
    }

    $event = array_merge($event, [
        'event_title' => $title,
        'description' => $description,
        'event_date' => $date,
        'event_time' => $time,
        'event_url' => $eventurl,
        'agenda' => $agenda,
    ]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Event | AUAF Events</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<main class="container py-5" style="max-width:760px">
    <h1 class="mb-4">Edit Event</h1>

    <?php foreach ($errors as $error): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endforeach; ?>

    <form method="post" class="card card-body shadow-sm">
        <div class="mb-3">
            <label class="form-label" for="event_title">Event title</label>
            <input class="form-control" id="event_title" name="event_title"
                   required value="<?= htmlspecialchars($event['event_title']) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label" for="description">Description</label>
            <textarea class="form-control" id="description" name="description"
                      rows="3" required><?= htmlspecialchars($event['description']) ?></textarea>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label" for="event_date">Date</label>
                <input class="form-control" type="date" id="event_date"
                       name="event_date" min="<?= date('Y-m-d') ?>" required
                       value="<?= htmlspecialchars($event['event_date']) ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label" for="event_time">Time</label>
                <input class="form-control" type="time" id="event_time"
                       name="event_time" required
                       value="<?= htmlspecialchars(substr($event['event_time'], 0, 5)) ?>">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label" for="event_url">Event URL</label>
            <input class="form-control" id="event_url" name="event_url" required
                   value="<?= htmlspecialchars($event['event_url']) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label" for="agenda">Agenda</label>
            <textarea class="form-control" id="agenda" name="agenda"
                      rows="4"><?= htmlspecialchars($event['agenda'] ?? '') ?></textarea>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-warning">Save Changes</button>
            <a href="/club/manage-events.php" class="btn btn-outline-secondary">Back</a>
        </div>
    </form>
</main>
</body>
</html>
