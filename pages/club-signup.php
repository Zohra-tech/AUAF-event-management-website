<?php
declare(strict_types=1);

session_start();

$pageTitle = 'Club Sign Up | AUAF Event Management';

$errors = [];
$success = false;

$clubName = '';
$email = '';
$description = '';
$googleSpaceUrl = '';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clubName = trim($_POST['club_name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $description = trim($_POST['description'] ?? '');
    $googleSpaceUrl = trim($_POST['google_space_url'] ?? '');

    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $csrfToken = $_POST['csrf_token'] ?? '';

    // Validate the form.
    if (!hash_equals($_SESSION['csrf_token'], $csrfToken)) {
        $errors[] = 'Your session has expired. Please refresh the page and try again.';
    }

    if ($clubName === '' || strlen($clubName) > 150) {
        $errors[] = 'Enter a club name (maximum 150 characters).';
    }

    if (
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        !str_ends_with($email, '@auaf.edu.af')
    ) {
        $errors[] = 'Use a valid AUAF email ending in @auaf.edu.af.';
    }

    if (strlen($password) < 8) {
        $errors[] = 'Your password must contain at least 8 characters.';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'The passwords do not match.';
    }

    if (strlen($description) > 2000) {
        $errors[] = 'The description cannot exceed 2,000 characters.';
    }

    if (
        $googleSpaceUrl !== '' &&
        (
            !filter_var($googleSpaceUrl, FILTER_VALIDATE_URL) ||
            !in_array(
                strtolower((string) parse_url($googleSpaceUrl, PHP_URL_SCHEME)),
                ['http', 'https'],
                true
            )
        )
    ) {
        $errors[] = 'Enter a valid Google Space invitation URL.';
    }

    if (!$errors) {
        try {
            require_once __DIR__ . '/../config/database.php';

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $sql = 'INSERT INTO clubs
                    (club_name, email, password_hash, description,
                     google_space_url, created_at)
                    VALUES
                    (:club_name, :email, :password_hash, :description,
                     :google_space_url, NOW())';

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':club_name' => $clubName,
                ':email' => $email,
                ':password_hash' => $passwordHash,
                ':description' => $description,
                ':google_space_url' => $googleSpaceUrl !== ''
                    ? $googleSpaceUrl
                    : null,
            ]);

            // Sign the new club in automatically.
            session_regenerate_id(true);

            $_SESSION['club_id'] = (int) $pdo->lastInsertId();
            $_SESSION['club_name'] = $clubName;

            unset($_SESSION['csrf_token']);

            header('Location: /club/dashboard.php');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $errors[] = 'This club email is already registered.';
            } else {
                error_log('Club registration failed: ' . $e->getMessage());
                $errors[] = 'Registration could not be completed. Check your database setup and try again.';
            }
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">

            <div class="text-center mb-4">
                <span class="hero-label"
                      style="background:#10294a;color:#f2d48b;">
                    CLUB PORTAL
                </span>

                <h1 class="mt-3"
                    style="color:#10294a;font-weight:800;">
                    Register Your Club
                </h1>

                <p class="text-secondary">
                    Create your club account to publish events and manage
                    student registrations at AUAF.
                </p>
            </div>

            <section class="feature-card">
                <?php if ($errors): ?>
                    <div class="alert alert-danger" role="alert">
                        <strong>Please correct the following:</strong>
                        <ul class="mb-0 mt-2">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" novalidate>
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>"
                    >

                    <div class="mb-3">
                        <label for="club_name" class="form-label fw-semibold">
                            Club Name
                        </label>
                        <input
                            type="text"
                            class="form-control"
                            id="club_name"
                            name="club_name"
                            maxlength="150"
                            value="<?= htmlspecialchars($clubName, ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="e.g. AUAF Technology Club"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">
                            Official AUAF Club Email
                        </label>
                        <input
                            type="email"
                            class="form-control"
                            id="email"
                            name="email"
                            value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="clubname@auaf.edu.af"
                            pattern="^[^@\s]+@auaf\.edu\.af$"
                            maxlength="254"
                            autocomplete="email"
                            required
                        >
                        <div class="form-text">
                            Use an email ending in @auaf.edu.af.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">
                            Club Description
                        </label>
                        <textarea
                            class="form-control"
                            id="description"
                            name="description"
                            rows="4"
                            maxlength="2000"
                            placeholder="Tell students about your club and its activities..."
                        ><?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="google_space_url" class="form-label fw-semibold">
                            Google Space Invitation Link
                        </label>
                        <input
                            type="url"
                            class="form-control"
                            id="google_space_url"
                            name="google_space_url"
                            value="<?= htmlspecialchars($googleSpaceUrl, ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="https://..."
                        >
                        <div class="form-text">
                            You can leave this blank and add the link later.
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label fw-semibold">
                                Create Password
                            </label>
                            <input
                                type="password"
                                class="form-control"
                                id="password"
                                name="password"
                                minlength="8"
                                autocomplete="new-password"
                                required
                            >
                            <div class="form-text">At least 8 characters.</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="confirm_password"
                                   class="form-label fw-semibold">
                                Confirm Password
                            </label>
                            <input
                                type="password"
                                class="form-control"
                                id="confirm_password"
                                name="confirm_password"
                                minlength="8"
                                autocomplete="new-password"
                                required
                            >
                        </div>
                    </div>

                    <button type="submit" class="btn btn-gold w-100 mt-3">
                        Create Club Account
                    </button>
                </form>

                <p class="text-center text-secondary mt-4 mb-0">
                    Already registered?
                    <a href="/pages/club-login.php">Sign in to your club dashboard</a>
                </p>
            </section>

        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
