<?php
session_start();
require_once __DIR__ . "/../config/connection.php";

if (!empty($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - CV Daya Satu</title>
    <link rel="icon" href="<?= $base_url ?>assets/images/logo.ico">
    <link rel="stylesheet" href="<?= $base_url ?>assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body>
    <main class="auth-page">
        <section class="auth-card">
            <div class="auth-mark">
                <img src="../assets/images/logo.png" alt="CV Daya Satu">
            </div>

            <div class="auth-head">
                <p class="auth-kicker">Admin Dashboard Access</p>
                <h1>Welcome Back</h1>
                <p class="auth-subtitle">Please log in to continue to the admin dashboard.</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="auth-alert" role="alert">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            <?php endif; ?>

            <form class="auth-form" action="check_login.php" method="post" autocomplete="off">
                <div class="field">
                    <label for="name" class="field-label">Username</label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        class="field-input"
                        placeholder="Enter your username"
                        required
                        autofocus>
                </div>

                <div class="field">
                    <label for="password" class="field-label">Password</label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="field-input"
                        placeholder="Enter your password"
                        required>
                </div>

                <div class="auth-bottom">
                    <span class="auth-note">Restricted access for administrators only.</span>
                    <button type="submit" class="btn-auth">
                        Login <i class="fa-solid fa-right-to-bracket"></i>
                    </button>
                </div>
            </form>
        </section>
    </main>
</body>

</html>