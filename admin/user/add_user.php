<?php

require_once __DIR__ . "/../security.php";
require_once __DIR__ . "/../../config/connection.php";

$errors = [];
$name = '';
$role = '';

if (isset($_POST['save'])) {

    $name     = trim($_POST['name'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = trim($_POST['role'] ?? '');

    if ($name === '') {
        $errors[] = 'User name is required.';
    }

    if ($password === '') {
        $errors[] = 'Password is required.';
    }

    if ($role === '') {
        $errors[] = 'User role is required.';
    }

    if (empty($errors)) {

        $sql = "
            INSERT INTO users
            (
                name, password, role, created_at
            )
            VALUES
            (
                ?, MD5(?), ?, NOW()
            )
        ";

        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                "ssi",
                $name,
                $password,
                $role
            );

            if (mysqli_stmt_execute($stmt)) {   
                mysqli_stmt_close($stmt);
                header("Location: users.php?success=added");
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
                Add User
            </div>

            <div class="page-description">
                Tambahkan user atau admin baru ke sistem.
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
                    Silakan isi data user baru di bawah ini dengan benar.
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
                        name="name"
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

                <div class="form-group">
                    <label class="form-label">
                        User Role
                    </label>
                    <select name="role" class="form-control" required>
                        <option value="">-- Select Role --</option>
                        <option value="1" <?= $role == '1' ? 'selected' : ''; ?>>Superadmin</option>
                        <option value="2" <?= $role == '2' ? 'selected' : ''; ?>>Admin</option>
                    </select>
                </div>

                <div class="action-group">
                    <button
                        type="submit"
                        name="save"
                        class="btn-add">
                        Add User
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </div>

            </form>

        </div>

    </div>

</div>