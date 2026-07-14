<?php

require_once __DIR__ . "/../../security.php";
require_once __DIR__ . "/../../../config/connection.php";

$errors = [];

$name = '';

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

$sql_categories = "SELECT * FROM categories ORDER BY category_name ASC";
$query_categories = mysqli_query($conn, $sql_categories);

if (isset($_POST['save'])) {

    $name = trim($_POST['brand_name'] ?? '');

    if ($name === '') {
        $errors[] = 'Brand name is required.';
    }

    if (empty($errors)) {

        $sql_check = "
        SELECT 1
        FROM brands
        WHERE brand_name = ?
        LIMIT 1
    ";

        $stmt_check = mysqli_prepare($conn, $sql_check);

        if ($stmt_check) {

            mysqli_stmt_bind_param($stmt_check, "s", $name);
            mysqli_stmt_execute($stmt_check);

            $result_check = mysqli_stmt_get_result($stmt_check);

            if (mysqli_fetch_assoc($result_check)) {
                $errors[] = 'Brand name already exists.';
            }

            mysqli_stmt_close($stmt_check);
        } else {
            $errors[] = 'Failed to validate brand.';
        }
    }

    if (empty($errors)) {

        $sql = "
            INSERT INTO brands
            (
                brand_name,
                followed_up_by
            )
            VALUES
            (
                ?,
                ?
            )
        ";

        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                "si",
                $name,
                $current_user_id
            );

            if (mysqli_stmt_execute($stmt)) {
                $folderName = strtolower($name);

                // Hapus karakter yang tidak valid untuk nama folder
                $folderName = preg_replace('/[\\\\\/:*?"<>|]/', '', $folderName);

                // Hilangkan spasi berlebih di awal/akhir dan jadikan satu spasi
                $folderName = preg_replace('/\s+/', ' ', trim($folderName));

                $folderPath = __DIR__ . "/../../../assets/images/products/" . $folderName;

                if (!is_dir($folderPath)) {
                    mkdir($folderPath, 0755, true);
                }

                header("Location: ../brands.php?success=added");
                exit;
            }

            $errors[] = 'Data failed to save.';
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
            <div class="page-title">Add Brand</div>
            <div class="page-description">
                Enter the brand details to create a new brand.
            </div>
        </div>

        <a href="../brands.php" class="btn-back">
            <i class="fa-solid fa-arrow-left"></i>
            Back to Brands
        </a>
    </div>

    <div class="card">

        <div class="card-header">
            <div>
                <div class="card-title">
                    Brand Information
                </div>

                <div class="card-subtitle">
                    This brand will be created by: <strong><?= htmlspecialchars($current_user_name ?: 'Current User', ENT_QUOTES, 'UTF-8'); ?></strong>
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
                        Brand Name
                    </label>

                    <input
                        type="text"
                        name="brand_name"
                        class="form-control"
                        value="<?= htmlspecialchars($name); ?>"
                        placeholder="Enter brand name"
                        required>
                </div>

                <div class="action-group">
                    <button
                        type="submit"
                        name="save"
                        class="btn-add">

                        Add Brand
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </div>

            </form>

        </div>

    </div>

</div>
<script>
    (() => {

        const customSelect = document.querySelector('[data-custom-select]');

        if (!customSelect) return;

        const trigger = customSelect.querySelector('[data-custom-select-trigger]');
        const menu = customSelect.querySelector('[data-custom-select-menu]');
        const label = customSelect.querySelector('[data-custom-select-label]');
        const options = customSelect.querySelectorAll('.custom-select-option');

        trigger.addEventListener('click', () => {
            customSelect.classList.toggle('open');
        });

        options.forEach(option => {

            option.addEventListener('click', () => {

                hiddenInput.value = option.dataset.value;
                label.textContent = option.dataset.label;

                options.forEach(item => {
                    item.classList.remove('is-selected');
                });

                option.classList.add('is-selected');

                customSelect.classList.remove('open');

            });

        });

        document.addEventListener('click', (event) => {

            if (!customSelect.contains(event.target)) {
                customSelect.classList.remove('open');
            }

        });

    })();
</script>