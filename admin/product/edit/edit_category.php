<?php

require_once __DIR__ . "/../../security.php";
require_once __DIR__ . "/../../../config/connection.php";

$errors = [];
$name = '';

$category_id = filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);

if (!$category_id) {
    header("Location: ../categories.php");
    exit;
}

$current_user_id = (int) ($_SESSION['user_id'] ?? 0);
$current_user_name = '';

if ($current_user_id > 0) {
    $sql_current_user = "SELECT username FROM users WHERE user_id = ? LIMIT 1";
    $stmt_current_user = mysqli_prepare($conn, $sql_current_user);

    if ($stmt_current_user) {
        mysqli_stmt_bind_param($stmt_current_user, "i", $current_user_id);
        mysqli_stmt_execute($stmt_current_user);
        $result_current_user = mysqli_stmt_get_result($stmt_current_user);

        if ($row_current_user = mysqli_fetch_assoc($result_current_user)) {
            $current_user_name = $row_current_user['username'] ?? '';
        }

        mysqli_stmt_close($stmt_current_user);
    }
}

$sql_get_category = "SELECT category_name FROM categories WHERE category_id = ? LIMIT 1";
$stmt_get_category = mysqli_prepare($conn, $sql_get_category);

if ($stmt_get_category) {
    mysqli_stmt_bind_param($stmt_get_category, "i", $category_id);
    mysqli_stmt_execute($stmt_get_category);
    $result_category = mysqli_stmt_get_result($stmt_get_category);
    $category_data = mysqli_fetch_assoc($result_category);
    mysqli_stmt_close($stmt_get_category);

    if (!$category_data) {
        header("Location: ../categories.php?error=not_found");
        exit;
    }

    if (!isset($_POST['save'])) {
        $name = $category_data['category_name'];
    }
}

if (isset($_POST['save'])) {

    $name = trim($_POST['category_name'] ?? '');

    if ($name === '') {
        $errors[] = 'Category name is required.';
    }

    if ($current_user_id <= 0) {
        $errors[] = 'Logged in user not found.';
    }

    if (empty($errors)) {

        $sql_check = "
        SELECT 1
        FROM categories
        WHERE LOWER(category_name) = LOWER(?)
          AND category_id <> ?
        LIMIT 1
    ";

        $stmt_check = mysqli_prepare($conn, $sql_check);

        if ($stmt_check) {

            mysqli_stmt_bind_param(
                $stmt_check,
                "si",
                $name,
                $category_id
            );

            mysqli_stmt_execute($stmt_check);

            $result_check = mysqli_stmt_get_result($stmt_check);

            if (mysqli_fetch_assoc($result_check)) {
                $errors[] = 'Category name already exists.';
            }

            mysqli_stmt_close($stmt_check);
        } else {
            $errors[] = 'Failed to validate category.';
        }
    }

    if (empty($errors)) {

        $sql = "
            UPDATE categories 
            SET category_name = ?,
            followed_up_by = ?,
            followed_up_at = NOW()
            WHERE category_id = ?
        ";

        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                "ssi",
                $name,
                $current_user_id,
                $category_id
            );

            if (mysqli_stmt_execute($stmt)) {
                header("Location: ../categories.php?success=updated");
                exit;
            }

            $errors[] = 'Data failed to update.';
            mysqli_stmt_close($stmt);
        } else {
            $errors[] = 'Query failed to prepare.';
        }
    }
}

require_once __DIR__ . "/../../partials/sidebar.php";

?>

<div class="content">

    <div class="page-header">

        <div>
            <div class="page-title">
                Edit Category
            </div>

            <div class="page-description">
                Update the details of an existing category.
            </div>
        </div>

        <a href="../categories.php" class="btn-back">
            <i class="fa-solid fa-arrow-left"></i>
            Back to Categories
        </a>

    </div>

    <div class="card">

        <div class="card-header">

            <div>
                <div class="card-title">
                    Category Information
                </div>
                <div class="card-subtitle">
                    This category is managed by: <strong><?= htmlspecialchars($current_user_name ?: 'Current User', ENT_QUOTES, 'UTF-8'); ?></strong>
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
                        Category Name
                    </label>

                    <input
                        type="text"
                        name="category_name"
                        class="form-control"
                        value="<?= htmlspecialchars($name); ?>"
                        placeholder="Enter category name"
                        required>

                </div>

                <div class="action-group">

                    <button
                        type="submit"
                        name="save"
                        class="btn-add">

                        Save Changes
                        <i class="fa-solid fa-floppy-disk"></i>

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>