<?php
require_once __DIR__ . "/../../security.php";
require_once __DIR__ . "/../../../config/connection.php";

$errors = [];
$success = false;

$brand_id = filter_input(INPUT_GET, 'brand_id', FILTER_VALIDATE_INT);

if (!$brand_id) {
    header("Location: ../brands.php");
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

$name = '';
$description = '';
$brand_data = null;

$sql_get_brand = "SELECT * FROM brands WHERE brand_id = ? LIMIT 1";
$stmt_get_brand = mysqli_prepare($conn, $sql_get_brand);

if ($stmt_get_brand) {
    mysqli_stmt_bind_param($stmt_get_brand, "i", $brand_id);
    mysqli_stmt_execute($stmt_get_brand);
    $result_brand = mysqli_stmt_get_result($stmt_get_brand);
    $brand_data = mysqli_fetch_assoc($result_brand);
    mysqli_stmt_close($stmt_get_brand);

    if (!$brand_data) {
        header("Location: ../brands.php?error=not_found");
        exit;
    }

    $oldBrandName = $brand_data['brand_name'];

    if (!isset($_POST['save'])) {
        $name        = $brand_data['brand_name'];
    }
}

if (isset($_POST['save'])) {

    $name = trim($_POST['brand_name'] ?? '');

    if ($name === '') {
        $errors[] = 'Brand name is required.';
    }

    if (empty($errors)) {
        $sql = "
            UPDATE brands
            SET brand_name = ?,
                followed_up_by = ?,
                followed_up_at = NOW()
            WHERE brand_id = ?
        ";

        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                "sii",
                $name,
                $current_user_id,
                $brand_id
            );

            if (mysqli_stmt_execute($stmt)) {

                // Nama folder lama
                $oldFolderName = strtolower($oldBrandName);
                $oldFolderName = preg_replace('/[\\\\\/:*?"<>|]/', '', $oldFolderName);
                $oldFolderName = preg_replace('/\s+/', ' ', trim($oldFolderName));

                // Nama folder baru
                $newFolderName = strtolower($name);
                $newFolderName = preg_replace('/[\\\\\/:*?"<>|]/', '', $newFolderName);
                $newFolderName = preg_replace('/\s+/', ' ', trim($newFolderName));

                $basePath = __DIR__ . "/../../../assets/images/products/";

                $oldFolderPath = $basePath . $oldFolderName;
                $newFolderPath = $basePath . $newFolderName;

                // Rename hanya jika nama berubah
                if (
                    $oldFolderName !== $newFolderName &&
                    is_dir($oldFolderPath) &&
                    !is_dir($newFolderPath)
                ) {
                    rename($oldFolderPath, $newFolderPath);
                }

                mysqli_stmt_close($stmt);

                header("Location: ../brands.php?success=updated");
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
            <div class="page-title">Edit Brand</div>
            <div class="page-description">
                Update the details of an existing brand.
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
                    This brand is managed by: <strong><?= htmlspecialchars($current_user_name ?: 'Current User', ENT_QUOTES, 'UTF-8'); ?></strong>
                </div>
            </div>
        </div>

        <div class="card-body">

            <?php if (!empty($errors)) : ?>
                <div class="alert alert-danger">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <div>
                        <?php foreach ($errors as $error) : ?>
                            <div><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
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
                        value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>"
                        placeholder="Enter brand name"
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

<script>
    (() => {
        const customSelect = document.querySelector('[data-custom-select]');
        if (!customSelect) return;

        const trigger = customSelect.querySelector('[data-custom-select-trigger]');
        const hiddenInput = customSelect.querySelector('#category_id');
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