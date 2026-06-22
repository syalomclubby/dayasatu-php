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
    $sql_current_user = "SELECT name FROM users WHERE user_id = ? LIMIT 1";
    $stmt_current_user = mysqli_prepare($conn, $sql_current_user);

    if ($stmt_current_user) {
        mysqli_stmt_bind_param($stmt_current_user, "i", $current_user_id);
        mysqli_stmt_execute($stmt_current_user);
        $result_current_user = mysqli_stmt_get_result($stmt_current_user);

        if ($row_current_user = mysqli_fetch_assoc($result_current_user)) {
            $current_user_name = $row_current_user['name'] ?? '';
        }

        mysqli_stmt_close($stmt_current_user);
    }
}

$sql_categories = "SELECT * FROM categories ORDER BY name ASC";
$query_categories = mysqli_query($conn, $sql_categories);

$category_id = '';
$name = '';
$description = '';
$category_name_label = 'Select Category';

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

    if (!isset($_POST['save'])) {
        $category_id = $brand_data['category_id'];
        $name        = $brand_data['name'];
        $description = $brand_data['description'];

        // Cari tahu nama kategori lamanya untuk label select menu
        $sql_old_cat = "SELECT name FROM categories WHERE category_id = ? LIMIT 1";
        $stmt_old_cat = mysqli_prepare($conn, $sql_old_cat);
        if ($stmt_old_cat) {
            mysqli_stmt_bind_param($stmt_old_cat, "i", $category_id);
            mysqli_stmt_execute($stmt_old_cat);
            $res_oc = mysqli_stmt_get_result($stmt_old_cat);
            if ($row_oc = mysqli_fetch_assoc($res_oc)) {
                $category_name_label = $row_oc['name'];
            }
            mysqli_stmt_close($stmt_old_cat);
        }
    }
}

if (isset($_POST['save'])) {

    $category_id = (int) ($_POST['category_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($category_id <= 0) {
        $errors[] = 'Category is required.';
    }

    if ($name === '') {
        $errors[] = 'Brand name is required.';
    }

    if ($description === '') {
        $errors[] = 'Description is required.';
    }

    if (empty($errors)) {
        $sql = "
            UPDATE brands
            SET category_id = ?,
                name = ?,
                description = ?,
                followed_up_by = ?,
                followed_up_at = NOW()
            WHERE brand_id = ?
        ";

        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                "issii",
                $category_id,
                $name,
                $description,
                $current_user_id,
                $brand_id
            );

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header("Location: ../brands.php?success=updated");
                exit;
            }

            $errors[] = 'Data failed to update. Error: ' . mysqli_stmt_error($stmt);
            mysqli_stmt_close($stmt);
        } else {
            $errors[] = 'Query failed to prepare. Error: ' . mysqli_error($conn);
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
                Ubah data brand yang sudah ada di sistem.
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
                    Data dikelola oleh: <strong><?= htmlspecialchars($current_user_name ?: 'Current User', ENT_QUOTES, 'UTF-8'); ?></strong>
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
                        Category
                    </label>

                    <div class="custom-select" data-custom-select>

                        <input
                            type="hidden"
                            name="category_id"
                            id="category_id"
                            value="<?= htmlspecialchars($category_id, ENT_QUOTES, 'UTF-8'); ?>">

                        <button
                            type="button"
                            class="custom-select-trigger"
                            data-custom-select-trigger>

                            <span data-custom-select-label>
                                <?= htmlspecialchars($category_name_label, ENT_QUOTES, 'UTF-8'); ?>
                            </span>

                            <i class="fa-solid fa-chevron-down"></i>
                        </button>

                        <div class="custom-select-menu" data-custom-select-menu>
                            <button type="button" class="custom-select-option is-placeholder" data-value="">
                                Select Category
                            </button>

                            <?php if ($query_categories) : ?>
                                <?php
                                mysqli_data_seek($query_categories, 0);
                                while ($category = mysqli_fetch_assoc($query_categories)) :
                                    $selected_class = ($category['category_id'] == $category_id) ? 'is-selected' : '';
                                ?>
                                    <button
                                        type="button"
                                        class="custom-select-option <?= $selected_class; ?>"
                                        data-value="<?= $category['category_id']; ?>"
                                        data-label="<?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8'); ?>">
                                        <?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8'); ?>
                                    </button>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Brand Name
                    </label>

                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>"
                        placeholder="Enter brand name"
                        required>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Description
                    </label>

                    <textarea
                        name="description"
                        class="form-control"
                        rows="6"
                        placeholder="Enter brand description"
                        required><?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?></textarea>
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