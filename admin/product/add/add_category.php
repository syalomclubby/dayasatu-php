<?php

require_once __DIR__ . "/../../security.php";
require_once __DIR__ . "/../../../config/connection.php";

$errors = [];
$name = '';

if (isset($_POST['save'])) {

    $name = trim($_POST['name'] ?? '');

    if ($name === '') {
        $errors[] = 'Category name is required.';
    }

    if (empty($errors)) {

        $sql = "
            INSERT INTO categories
            (
                name
            )
            VALUES
            (
                ?
            )
        ";

        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {

            mysqli_stmt_bind_param(
                $stmt,
                "s",
                $name
            );

            if (mysqli_stmt_execute($stmt)) {

                mysqli_stmt_close($stmt);

                header("Location: ../categories.php");
                exit;
            }

            mysqli_stmt_close($stmt);

            $errors[] = 'Data failed to save.';
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
                Add Category
            </div>

            <div class="page-description">
                Tambahkan category baru ke sistem.
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
                    Lengkapi data category di bawah ini.
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
                        name="name"
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

                        Add Category
                        <i class="fa-solid fa-plus"></i>

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>