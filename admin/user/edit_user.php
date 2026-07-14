<?php

require_once __DIR__ . "/../security.php";
require_once __DIR__ . "/../../config/connection.php";

$errors = [];
$name = '';
$role = '';

$user_id = filter_input(INPUT_GET, 'user_id', FILTER_VALIDATE_INT);

if (!$user_id) {
    header("Location: users.php");
    exit;
}

$sql_get_user = "SELECT * FROM users WHERE user_id = ? LIMIT 1";
$stmt_get_user = mysqli_prepare($conn, $sql_get_user);

if ($stmt_get_user) {
    mysqli_stmt_bind_param($stmt_get_user, "i", $user_id);
    mysqli_stmt_execute($stmt_get_user);
    $result_user = mysqli_stmt_get_result($stmt_get_user);
    $user_data = mysqli_fetch_assoc($result_user);
    mysqli_stmt_close($stmt_get_user);

    if (!$user_data) {
        header("Location: users.php?error=not_found");
        exit;
    }

    if (!isset($_POST['save'])) {
        $name = $user_data['username'];
        $password = $user_data['password'];
        $role = $user_data['role'];
    }
}

if (isset($_POST['save'])) {

    $name     = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = trim($_POST['role'] ?? '');

    if ($name === '') {
        $errors[] = 'User name is required.';
    }

    if ($password === '') {
        $errors[] = 'Password is required.';
    }


    if (empty($errors)) {

        $sql = "
            UPDATE users 
            SET username = ?, password = MD5(?), role = role
            WHERE user_id = ?
        ";

        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                "ssi",
                $name,
                $password,
                $user_id
            );

            if (mysqli_stmt_execute($stmt)) {   
                mysqli_stmt_close($stmt);
                header("Location: users.php?success=updated");
                exit;
            }

            $errors[] = 'Data failed to save.';
            mysqli_stmt_close($stmt);
        } else {
            $errors[] = 'Query failed to prepare.';
        }
    }
}

require_once __DIR__ . "/../partials/sidebar.php";

?>
<div class="content">

    <div class="page-header">

        <div>
            <div class="page-title">
                Edit User
            </div>

            <div class="page-description">
                Change existing user data in the system.
            </div>
        </div>

        <a href="../user/users.php" class="btn-back">
            <i class="fa-solid fa-arrow-left"></i>
            Back to Users
        </a>

    </div>

    <div class="card">

        <div class="card-header">
            <div>
                <div class="card-title">
                    User Information
                </div>
                <div class="card-subtitle">
                    Please fill in the new user data below correctly.
                </div>
            </div>
        </div>

        <div class="card-body">

            <?php if (!empty($errors)) : ?>
                <div class="alert alert-danger">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <div>
                        <?php foreach ($errors as $error) : ?>
                            <div><?= htmlspecialchars($error); ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST">

                <div class="form-group">
                    <label class="form-label">
                        User Name
                    </label>
                    <input
                        type="text"
                        name="username"
                        class="form-control"
                        value="<?= htmlspecialchars($name); ?>"
                        placeholder="Enter user name"
                        required>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        User Password
                    </label>
                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        placeholder="Enter password"
                        required>
                </div>

                <div class="action-group">
                    <button
                        type="submit"
                        name="save"
                        class="btn-add">
                        Edit User
                        <i class="fa-solid fa-floppy-disk"></i>
                    </button>
                </div>

            </form>

        </div>

    </div>

</div>

<script>
(() => {
        document.querySelectorAll('[data-custom-select]').forEach((customSelect) => {
            const trigger = customSelect.querySelector('[data-custom-select-trigger]');
            const hiddenInput = customSelect.querySelector('input[type="hidden"]');
            const label = customSelect.querySelector('[data-custom-select-label]');
            const options = customSelect.querySelectorAll('.custom-select-option');

            if (!trigger || !hiddenInput || !label || !options.length) return;

            const closeMenu = () => customSelect.classList.remove('open');

            trigger.addEventListener('click', (e) => {
                e.preventDefault();
                customSelect.classList.toggle('open');
            });

            options.forEach((option) => {
                option.addEventListener('click', () => {
                    const value = option.dataset.value || '';
                    const text = option.dataset.label || option.textContent.trim();

                    hiddenInput.value = value;
                    label.textContent = value === '' ? 'Select option' : text;

                    options.forEach((item) => item.classList.remove('is-selected'));
                    option.classList.add('is-selected');

                    closeMenu();
                });
            });

            document.addEventListener('click', (event) => {
                if (!customSelect.contains(event.target)) {
                    closeMenu();
                }
            });
        });
    })();
    </script>