<?php
session_start();

require_once __DIR__ . '/../config/database.php';

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    // Validate required fields.
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

            // Find the club account.
            $stmt = $pdo->prepare(
                'SELECT club_id, club_name, email, password_hash
                 FROM clubs
                 WHERE LOWER(TRIM(email)) = :email
                 LIMIT 1'
            );

            $stmt->execute([
                'email' => $email
            ]);

            $club = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$club) {

                $error = 'No club account was found with this email. Please register first.';

            } elseif (!password_verify($password, $club['password_hash'])) {

                $error = 'Incorrect password. Please try again.';

            } else {

                // Successful login.
                session_regenerate_id(true);

                $_SESSION['club_id'] = (int) $club['club_id'];
                $_SESSION['club_name'] = $club['club_name'];
                $_SESSION['club_email'] = $club['email'];

                // Redirect to club dashboard.
                header('Location: dashboard.php');
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

    <title>Club Login | AUAF</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <style>
        body {
            min-height: 100vh;
            background: #f4f5f8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        .login-card {
            width: 100%;
            max-width: 450px;
            background: #fff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 35px rgba(0,0,0,0.08);
        }

        .login-icon {
            width: 65px;
            height: 65px;
            background: #10294b;
            color: #d6ad45;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin: 0 auto 20px;
        }

        .login-title {
            color: #10294b;
            font-weight: 700;
        }

        .form-label {
            color: #26364b;
            font-weight: 600;
        }

        .form-control {
            padding: 12px;
            border-radius: 8px;
        }

        .login-btn {
            background: #10294b;
            color: white;
            padding: 13px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            width: 100%;
            cursor: pointer;
            transition: 0.3s;
        }

        .login-btn:hover {
            background: #d6ad45;
            color: #10294b;
        }

        .register-link {
            color: #10294b;
            font-weight: 600;
            text-decoration: none;
        }

        .register-link:hover {
            color: #b58b28;
        }
    </style>
</head>

<body>

<div class="login-card">

    <div class="login-icon">
        <i class="bi bi-person-lock"></i>
    </div>

    <div class="text-center mb-4">
        <h2 class="login-title">Club Login</h2>

        <p class="text-muted">
            Sign in to manage your AUAF club events.
        </p>
    </div>

    <?php if ($error !== ''): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="login.php">

        <div class="mb-3">
            <label for="email" class="form-label">
                AUAF Club Email
            </label>

            <input
                type="email"
                name="email"
                id="email"
                class="form-control"
                placeholder="club@auaf.edu.af"
                value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
                maxlength="254"
                autocomplete="username"
                required
            >
        </div>

        <div class="mb-4">
            <label for="password" class="form-label">
                Password
            </label>

            <input
                type="password"
                name="password"
                id="password"
                class="form-control"
                placeholder="Enter your password"
                autocomplete="current-password"
                required
            >
        </div>

        <button type="submit" class="login-btn">
            <i class="bi bi-box-arrow-in-right me-2"></i>
            Sign In
        </button>

    </form>

    <div class="text-center mt-4">
        <p class="text-muted mb-2">
            Don't have a club account?
        </p>

        <a href="register.php" class="register-link">
            Register Your Club
        </a>
    </div>

    <div class="text-center mt-3">
        <a href="../index.php" class="text-muted text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i>
            Back to Home
        </a>
    </div>

</div>

</body>
</html>