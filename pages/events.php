
<?php
$pageTitle = "Upcoming Events | AUAF";

// No sample events. The database integration will be added later.
$events = [];

$categories = [];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Browse academic, cultural, and student club events at AUAF.">
    <title><?= htmlspecialchars($pageTitle) ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
          rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
    
</head>

<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark site-navbar">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="../index.php">
            <span class="brand-mark">A</span>
            <span class="brand-text">
                AUAF
                <small>EVENTS & ACTIVITIES</small>
            </span>
        </a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#mainNavigation"
                aria-controls="mainNavigation" aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavigation">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-3">
                <li class="nav-item">
                    <a class="nav-link" href="../index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="events.php">Events</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="clubs.php">Student Clubs</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../index.php#about">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="login.php">Sign In</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-gold" href="#event-list">
                        Find an Event <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Page heading -->
<section class="events-page-hero">
    <div class="container">
        <div class="events-breadcrumb">
            <a href="../index.php">Home</a>
            <i class="bi bi-chevron-right"></i>
            <span>Events</span>
        </div>

        <span class="section-eyebrow">YOUR CAMPUS, YOUR EXPERIENCE</span>
        <h1>Discover What's <span>Happening.</span></h1>
        <p>
            Explore university events, student activities, workshops,
            and opportunities to connect with the AUAF community.
        </p>
    </div>
</section>

<!-- Event search and filters -->
<section class="event-browser" id="event-list">
    <div class="container">

        <div class="event-browser-heading">
            <div>
                <span class="section-eyebrow">EXPLORE AND PARTICIPATE</span>
                <h2 class="section-title">Browse All Events</h2>
            </div>
            <span class="event-total">
                <i class="bi bi-calendar2-week"></i>
                <span id="event-count"><?= count($events) ?></span> events
            </span>
        </div>

        <div class="event-filter-panel">
            <div class="row g-3 align-items-end">
                <div class="col-lg-6">
                    <label for="eventSearch" class="form-label">Search events</label>
                    <div class="event-search-input">
                        <i class="bi bi-search"></i>
                        <input type="search" id="eventSearch"
                               class="form-control"
                               placeholder="Search by event, club, or keyword..."
                               autocomplete="off">
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <label for="categoryFilter" class="form-label">Category</label>
                    <select id="categoryFilter" class="form-select">
                        <option value="all">All categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= htmlspecialchars(strtolower($category)) ?>">
                                <?= htmlspecialchars($category) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <button type="button" id="clearFilters"
                            class="btn btn-outline-secondary clear-filter-btn w-100">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                        Clear filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Event cards -->
        <div class="row g-4 event-results" id="eventResults">

            <?php foreach ($events as $event): ?>
                <?php
                    $timestamp = strtotime($event["date"]);
                    $searchText = strtolower(
                        $event["title"] . " " .
                        $event["category"] . " " .
                        $event["club"] . " " .
                        $event["description"] . " " .
                        $event["location"]
                    );
                ?>

                <div class="col-md-6 col-lg-4 event-item"
                     data-category="<?= htmlspecialchars(strtolower($event["category"])) ?>"
                     data-search="<?= htmlspecialchars($searchText, ENT_QUOTES, 'UTF-8') ?>">

                    <article class="event-card event-list-card">
                        <div class="event-card-art">
                            <div class="event-art-icon">
                                <i class="bi <?= htmlspecialchars($event["icon"]) ?>"></i>
                            </div>

                            <span class="event-category">
                                <?= htmlspecialchars($event["category"]) ?>
                            </span>

                            <div class="event-date-badge">
                                <span><?= strtoupper(date("M", $timestamp)) ?></span>
                                <strong><?= date("d", $timestamp) ?></strong>
                            </div>
                        </div>

                        <div class="event-card-body">
                            <div class="event-date">
                                <i class="bi bi-clock"></i>
                                <?= date("l, F j", $timestamp) ?>
                                &bull;
                                <?= htmlspecialchars($event["time"]) ?>
                            </div>

                            <h3><?= htmlspecialchars($event["title"]) ?></h3>

                            <p><?= htmlspecialchars($event["description"]) ?></p>

                            <div class="event-meta">
                                <span>
                                    <i class="bi bi-people"></i>
                                    <?= htmlspecialchars($event["club"]) ?>
                                </span>
                                <span>
                                    <i class="bi bi-geo-alt"></i>
                                    <?= htmlspecialchars($event["location"]) ?>
                                </span>
                            </div>

                            <a href="events.php?id=<?= (int)$event["id"] ?>"
                               class="event-link">
                                View event details
                                <i class="bi bi-arrow-up-right"></i>
                            </a>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>

        </div>

        <!-- Empty state -->
        <div id="noEvents" class="no-events d-none">
            <i class="bi bi-calendar-x"></i>
            <h3>No events found</h3>
            <p>Try another keyword or select a different category.</p>
            <button type="button" id="emptyClearFilters" class="btn btn-gold">
                Show all events
            </button>
        </div>
    </div>
</section>

<!-- Footer -->

<!-- Footer -->
<footer class="site-footer">
    <div class="container">
        <div class="row gy-4">
            <div class="col-lg-6">
                <a class="footer-brand" href="../index.php">AUAF EVENTS</a>
                <p class="footer-description">
                    Discover university events, student organizations,
                    and opportunities to connect with the AUAF community.
                </p>
            </div>

            <div class="col-12 col-lg-6 footer-explore">
                <h4>Explore</h4>
                <a href="../index.php">Home</a>
                <a href="events.php">Upcoming Events</a>
                <a href="clubs.php">Student Clubs</a>
            </div>
        </div>

        <div class="footer-bottom">
            <span>&copy; <?= date("Y") ?> AUAF Event Management Website</span>
            <span>Designed for the AUAF community</span>
        </div>
    </div>
</footer>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/main.js"></script>

</body>
</html>

