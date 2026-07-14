<?php

require_once __DIR__ . "/../../security.php";
require_once __DIR__ . "/../../../config/connection.php";

$errors = [];
$success = false;

$sql_categories = "SELECT category_id, category_name FROM categories ORDER BY category_name ASC";
$query_categories = mysqli_query($conn, $sql_categories);

$category_id = '';

$product_id = filter_input(INPUT_GET, 'product_id', FILTER_VALIDATE_INT);

$product_data = null;

if (!$product_id) {
    header("Location: ../products.php");
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

$sql_brand = "SELECT brand_id, brand_name FROM brands ORDER BY brand_name ASC";
$query_brand = mysqli_query($conn, $sql_brand);

$brand_id = '';
$name = '';
$description = '';
$old_image = '';
$old_brand_folder = '';
$old_brand_id = '';
$brand_name_label = 'Select brand';

$category_name_label = 'Select category';

$sql_get_product = "SELECT * FROM products WHERE product_id = ? LIMIT 1";
$stmt_get_product = mysqli_prepare($conn, $sql_get_product);

if ($stmt_get_product) {
    mysqli_stmt_bind_param($stmt_get_product, "i", $product_id);
    mysqli_stmt_execute($stmt_get_product);
    $result_product = mysqli_stmt_get_result($stmt_get_product);
    $product_data = mysqli_fetch_assoc($result_product);
    mysqli_stmt_close($stmt_get_product);

    if (!$product_data) {
        header("Location: ../products.php?error=not_found");
        exit;
    }

    if (!isset($_POST['save'])) {
        $brand_id = $product_data['brand_id'];
        $old_brand_id = $product_data['brand_id'];
        $category_id = $product_data['category_id'];
        $sql_old_category = "SELECT category_name FROM categories WHERE category_id = ? LIMIT 1";
        $stmt_old_category = mysqli_prepare($conn, $sql_old_category);

        if ($stmt_old_category) {
            mysqli_stmt_bind_param($stmt_old_category, "i", $category_id);
            mysqli_stmt_execute($stmt_old_category);

            $res_oc = mysqli_stmt_get_result($stmt_old_category);

            if ($row_oc = mysqli_fetch_assoc($res_oc)) {
                $category_name_label = $row_oc['category_name'];
            }

            mysqli_stmt_close($stmt_old_category);
        }

        $name        = $product_data['product_name'];
        $description = $product_data['product_description'];
        $old_image   = $product_data['product_image'];

        $sql_old_brand = "SELECT brand_name FROM brands WHERE brand_id = ? LIMIT 1";
        $stmt_old_brand = mysqli_prepare($conn, $sql_old_brand);
        if ($stmt_old_brand) {
            mysqli_stmt_bind_param($stmt_old_brand, "i", $brand_id);
            mysqli_stmt_execute($stmt_old_brand);
            $res_ob = mysqli_stmt_get_result($stmt_old_brand);
            if ($row_ob = mysqli_fetch_assoc($res_ob)) {
                $brand_name_label = $row_ob['brand_name'];
                $old_brand_folder = strtolower(trim($row_ob['brand_name']));
                $old_brand_folder = preg_replace('/[\\\\\/:*?"<>|]/', '', $old_brand_folder);
                $old_brand_folder = preg_replace('/\s+/', ' ', trim($old_brand_folder));
            }
            mysqli_stmt_close($stmt_old_brand);
        }
    }
}

function slugify_filename(string $text): string
{
    $text = trim($text);
    if ($text === '') return 'product';
    $text = mb_strtolower($text, 'UTF-8');
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    return $text !== '' ? $text : 'product';
}

function save_product_image(array $file, string $baseName, string $uploadDir): array
{
    if (!isset($file['error']) || is_array($file['error'])) {
        return [false, null, 'Invalid upload data.'];
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [false, null, 'Image upload failed.'];
    }
    if (!is_uploaded_file($file['tmp_name'])) {
        return [false, null, 'Invalid uploaded file.'];
    }
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
        return [false, null, 'Upload directory could not be created.'];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);

    $allowedMime = ['image/png', 'image/jpeg', 'image/jpg', 'image/webp'];
    if (!in_array($mime, $allowedMime, true)) {
        return [false, null, 'Only PNG, JPG, JPEG, or WEBP images are allowed.'];
    }

    $filenameBase = slugify_filename($baseName) . '-' . date('YmdHis') . '-' . random_int(1000, 9999);
    $targetPath = rtrim($uploadDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filenameBase . '.png';

    if ($mime === 'image/png') {
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            return [false, null, 'Failed to save PNG image.'];
        }
        return [true, $filenameBase, null];
    }

    if (!function_exists('imagecreatefromjpeg') || !function_exists('imagepng')) {
        return [false, null, 'Server image conversion support is unavailable.'];
    }

    switch ($mime) {
        case 'image/jpeg':
        case 'image/jpg':
            $source = imagecreatefromjpeg($file['tmp_name']);
            break;
        case 'image/webp':
            if (!function_exists('imagecreatefromwebp')) {
                return [false, null, 'WEBP conversion is not supported by the server.'];
            }
            $source = imagecreatefromwebp($file['tmp_name']);
            break;
        default:
            $source = false;
            break;
    }

    if (!$source) {
        return [false, null, 'Failed to read image file.'];
    }

    imagepalettetotruecolor($source);
    imagealphablending($source, true);
    imagesavealpha($source, true);

    if (!imagepng($source, $targetPath)) {
        imagedestroy($source);
        return [false, null, 'Failed to convert image to PNG.'];
    }

    imagedestroy($source);
    return [true, $filenameBase, null];
}

if (isset($_POST['save'])) {
    $brand_id    =
        ($_POST['brand_id'] ?? '');
    $category_id = trim($_POST['category_id'] ?? '');
    if ($category_id !== '') {

        $sql_category_name = "
        SELECT category_name
        FROM categories
        WHERE category_id = ?
        LIMIT 1
    ";

        $stmt_category_name = mysqli_prepare(
            $conn,
            $sql_category_name
        );

        if ($stmt_category_name) {

            mysqli_stmt_bind_param(
                $stmt_category_name,
                "i",
                $category_id
            );

            mysqli_stmt_execute($stmt_category_name);

            $result_category_name =
                mysqli_stmt_get_result($stmt_category_name);

            if ($row_category_name =
                mysqli_fetch_assoc($result_category_name)
            ) {

                $category_name_label =
                    $row_category_name['category_name'];
            }

            mysqli_stmt_close($stmt_category_name);
        }
    }

    $name        = trim($_POST['product_name'] ?? '');
    $description = trim($_POST['product_description'] ?? '');

    if ($brand_id === '') {
        $errors[] = 'Brand is required.';
    }

    if ($category_id === '') {
        $errors[] = 'Category is required.';
    }

    if ($name === '') {
        $errors[] = 'Name is required.';
    }

    if ($description === '') {
        $errors[] = 'Description is required.';
    }

    if ($current_user_id <= 0) {
        $errors[] = 'Logged in user not found.';
    }

    $image_base = $product_data['product_image'];
    $has_new_image = isset($_FILES['image_file']) && !empty($_FILES['image_file']['name']);


    // Ambil brand lama saat submit
    $old_brand_id = $product_data['brand_id'];
    $old_brand_folder = '';

    $sqlOldBrand = "
    SELECT brand_name 
    FROM brands 
    WHERE brand_id = ?
    LIMIT 1
";

    $stmtOldBrand = mysqli_prepare($conn, $sqlOldBrand);

    if ($stmtOldBrand) {

        mysqli_stmt_bind_param(
            $stmtOldBrand,
            "i",
            $old_brand_id
        );

        mysqli_stmt_execute($stmtOldBrand);

        $resultOldBrand = mysqli_stmt_get_result($stmtOldBrand);

        if ($rowOldBrand = mysqli_fetch_assoc($resultOldBrand)) {

            $old_brand_folder = strtolower(trim($rowOldBrand['brand_name']));
            $old_brand_folder = preg_replace('/[\\\\\/:*?"<>|]/', '', $old_brand_folder);
            $old_brand_folder = preg_replace('/\s+/', ' ', trim($old_brand_folder));
        }

        mysqli_stmt_close($stmtOldBrand);
    }

    if (empty($errors)) {
        $sqlBrandName = "SELECT brand_name FROM brands WHERE brand_id = ? LIMIT 1";
        $stmtBrandName = mysqli_prepare($conn, $sqlBrandName);
        mysqli_stmt_bind_param($stmtBrandName, "i", $brand_id);
        mysqli_stmt_execute($stmtBrandName);
        $resultBrandName = mysqli_stmt_get_result($stmtBrandName);
        $brandData = mysqli_fetch_assoc($resultBrandName);
        mysqli_stmt_close($stmtBrandName);

        if (!$brandData) {
            $errors[] = 'Brand not found.';
        } else {

            $brandFolder = strtolower(trim($brandData['brand_name']));
            $brandFolder = preg_replace('/[\\\\\/:*?"<>|]/', '', $brandFolder);
            $brandFolder = preg_replace('/\s+/', ' ', trim($brandFolder));

            $uploadDir = __DIR__ . '/../../../assets/images/products/' . $brandFolder . '/';


            // Pindahkan gambar jika brand berubah dan tidak upload gambar baru
            if (
                $old_brand_id != $brand_id &&
                !$has_new_image &&
                !empty($product_data['product_image'])
            ) {

                $oldPath =
                    __DIR__ .
                    '/../../../assets/images/products/' .
                    $old_brand_folder .
                    '/' .
                    $product_data['product_image'] .
                    '.png';


                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }


                $newPath =
                    $uploadDir .
                    $product_data['product_image'] .
                    '.png';


                if (file_exists($oldPath)) {
                    rename($oldPath, $newPath);
                }
            }


            // Upload gambar baru
            if ($has_new_image) {

                [$ok, $savedBaseName, $uploadError] = save_product_image(
                    $_FILES['image_file'],
                    $name,
                    $uploadDir
                );

                if (!$ok) {

                    $errors[] = $uploadError;
                } else {

                    $image_base = $savedBaseName;

                    if (!empty($product_data['product_image'])) {

                        $oldFilePath =
                            __DIR__ .
                            '/../../../assets/images/products/' .
                            $old_brand_folder .
                            '/' .
                            $product_data['product_image'] .
                            '.png';


                        if (file_exists($oldFilePath)) {
                            unlink($oldFilePath);
                        }
                    }
                }
            }
        }
    }

    if (empty($errors)) {
        $sql = "UPDATE products 
                SET brand_id = ?, category_id = ?, product_name = ?, product_description = ?, product_image = ?, followed_up_at = NOW(), followed_up_by = ?
                WHERE product_id = ?";

        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                "iisssii",
                $brand_id,
                $category_id,
                $name,
                $description,
                $image_base,
                $current_user_id,
                $product_id
            );

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header("Location: ../products.php?success=updated");
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
            <div class="page-title">Edit Product</div>
            <div class="page-description">Update the details of an existing product.</div>
        </div>

        <a href="../products.php" class="btn-back">
            <i class="fa-solid fa-arrow-left"></i> Back to Products
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Product Information</div>
                <div class="card-subtitle">
                    This product is managed by: <strong><?= htmlspecialchars($current_user_name ?: 'Current User', ENT_QUOTES, 'UTF-8'); ?></strong>
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

            <form method="POST" enctype="multipart/form-data">
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Brand</label>
                        <div class="custom-select" data-custom-select>
                            <input type="hidden" name="brand_id" id="brand_id" value="<?= htmlspecialchars($brand_id, ENT_QUOTES, 'UTF-8'); ?>">

                            <button type="button" class="custom-select-trigger" data-custom-select-trigger>
                                <span data-custom-select-label>
                                    <?= htmlspecialchars($brand_name_label, ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>

                            <div class="custom-select-menu" data-custom-select-menu>
                                <button type="button" class="custom-select-option is-placeholder" data-value="">
                                    Select brand
                                </button>

                                <?php if ($query_brand) : ?>
                                    <?php
                                    mysqli_data_seek($query_brand, 0);
                                    while ($brand = mysqli_fetch_assoc($query_brand)) :
                                        $selected_class = ($brand['brand_id'] == $brand_id) ? 'is-selected' : '';
                                    ?>
                                        <button
                                            type="button"
                                            class="custom-select-option <?= $selected_class; ?>"
                                            data-value="<?= (int) $brand['brand_id']; ?>"
                                            data-label="<?= htmlspecialchars($brand['brand_name'], ENT_QUOTES, 'UTF-8'); ?>">
                                            <?= htmlspecialchars($brand['brand_name'], ENT_QUOTES, 'UTF-8'); ?>
                                        </button>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Category</label>

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
                                            data-label="<?= htmlspecialchars($category['category_name'], ENT_QUOTES, 'UTF-8'); ?>">
                                            <?= htmlspecialchars($category['category_name'], ENT_QUOTES, 'UTF-8'); ?>
                                        </button>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </div>

                        </div>
                    </div>

                </div>

                <div class="form-group">
                    <label for="name" class="form-label">Name</label>
                    <input
                        type="text"
                        name="product_name"
                        id="name"
                        class="form-control"
                        value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>"
                        placeholder="Enter product name"
                        required>
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Description</label>
                    <textarea
                        name="product_description"
                        id="description"
                        class="form-control"
                        placeholder="Enter product description"
                        required><?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Image</label>
                        <div class="file-dropzone" id="fileDropzone">
                            <input
                                type="file"
                                name="image_file"
                                id="image_file"
                                class="file-input"
                                accept="image/png,image/jpeg,image/jpg,image/webp">

                            <div class="file-dropzone-inner">
                                <i class="fa-regular fa-image"></i>
                                <div class="file-dropzone-title">Drag new image here or click to change</div>
                                <div class="file-dropzone-hint">PNG, JPG, JPEG, WEBP</div>
                                <div class="file-dropzone-btn">Choose File</div>
                            </div>
                        </div>
                        <div class="form-helper">Leave empty to keep old image.</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Preview</label>
                        <?php
                        $has_old_img = !empty($old_image) && !empty($old_brand_folder);

                        $old_img_src = $has_old_img
                            ? "../../../assets/images/products/" . $old_brand_folder . "/" . $old_image . ".png"
                            : "";
                        ?>
                        <div class="image-preview <?= $has_old_img ? '' : 'is-hidden'; ?>" id="imagePreview">
                            <img id="imagePreviewImg" src="<?= $old_img_src; ?>" alt="Image Preview">
                        </div>
                    </div>
                </div>

                <div class="action-group">
                    <button type="submit" name="save" class="btn-add">
                        Save Changes
                        <i class="fa-solid fa-floppy-disk"></i>
                    </button>

                    <a
                        href="../products.php"
                        class="btn-back"> Cancel &nbsp
                        <i class="fas fa-times"></i>
                    </a>
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

    document.addEventListener('DOMContentLoaded', () => {

        const imageInput = document.getElementById('image_file');
        const preview = document.getElementById('imagePreview');
        const previewImg = document.getElementById('imagePreviewImg');

        if (!imageInput || !preview || !previewImg) {
            return;
        }

        imageInput.addEventListener('change', function() {

            const file = this.files[0];

            if (!file) {
                return;
            }

            const reader = new FileReader();

            reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.classList.remove('is-hidden');
            };

            reader.readAsDataURL(file);

        });

    });
</script>