<?php
$pageTitle = $pageTitle ?? 'AUAF Event Management';
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

    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<nav class="site-navbar">
    <div class="container d-flex justify-content-between align-items-center flex-wrap gap-3">
        <a class="brand" href="/index.php">AUAF EVENTS</a>

        <div class="nav-links">
            <a href="/index.php">Home</a>
            <a href="/pages/events.php">Events</a>
            <a href="/pages/clubs.php">Clubs</a>
            <a href="/pages/login.php">Sign In</a>
            <a href="/pages/club-signup.php">Club Sign Up</a>
        </div>
    </div>
</nav>
