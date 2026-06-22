<?php
require_once __DIR__ . "/../../security.php";
require_once __DIR__ . "/../../../config/connection.php";

$errors = [];
$success = false;

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

$sql_brand = "SELECT brand_id, name FROM brands ORDER BY name ASC";
$query_brand = mysqli_query($conn, $sql_brand);

$brand_id = '';
$name = '';
$description = '';
$price = '';

function slugify_filename(string $text): string
{
    $text = trim($text);

    if ($text === '') {
        return 'product';
    }

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

    $allowedMime = [
        'image/png',
        'image/jpeg',
        'image/jpg',
        'image/webp',
    ];

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
    $brand_id = trim($_POST['brand_id'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = trim($_POST['price'] ?? '');

    if ($brand_id === '') {
        $errors[] = 'Brand is required.';
    }

    if ($name === '') {
        $errors[] = 'Name is required.';
    }

    if ($description === '') {
        $errors[] = 'Description is required.';
    }

    if ($price === '') {
        $errors[] = 'Price is required.';
    } elseif (!is_numeric($price)) {
        $errors[] = 'Price must be numeric.';
    }

    if ($current_user_id <= 0) {
        $errors[] = 'Logged in user not found.';
    }

    if (!isset($_FILES['image_file']) || empty($_FILES['image_file']['name'])) {
        $errors[] = 'Image is required.';
    }

    $image_base = null;

    if (empty($errors) && isset($_FILES['image_file'])) {

        $sqlBrandName = "SELECT name FROM brands WHERE brand_id = ? LIMIT 1";
        $stmtBrandName = mysqli_prepare($conn, $sqlBrandName);

        mysqli_stmt_bind_param($stmtBrandName, "i", $brand_id);
        mysqli_stmt_execute($stmtBrandName);

        $resultBrandName = mysqli_stmt_get_result($stmtBrandName);
        $brandData = mysqli_fetch_assoc($resultBrandName);

        mysqli_stmt_close($stmtBrandName);

        if (!$brandData) {
            $errors[] = 'Brand not found.';
        } else {

            $brandFolder = strtolower(trim($brandData['name']));

            $uploadDir = __DIR__
                . '/../../../assets/images/products/'
                . $brandFolder
                . '/';

            [$ok, $savedBaseName, $uploadError] = save_product_image(
                $_FILES['image_file'],
                $name,
                $uploadDir
            );

            if (!$ok) {
                $errors[] = $uploadError;
            } else {
                $image_base = $savedBaseName;
            }
        }
    }

    if (empty($errors)) {
        $sql = "INSERT INTO products (brand_id, name, description, price, image, followed_up_by)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                "issssi",
                $brand_id,
                $name,
                $description,
                $price,
                $image_base,
                $current_user_id
            );

            if (mysqli_stmt_execute($stmt)) {
                header("Location: ../products.php");
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
            <div class="page-title">Add Product</div>
            <div class="page-description">Tambahkan produk baru ke sistem.</div>
        </div>

        <a href="../products.php" class="btn-back">
            <i class="fa-solid fa-arrow-left"></i>
            Back to Products
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Product Information</div>
                <div class="card-subtitle">
                    Data followed up by akan otomatis memakai akun login saat ini: <strong><?= htmlspecialchars($current_user_name ?: 'Current User', ENT_QUOTES, 'UTF-8'); ?></strong>
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
                                    <?= $brand_id ? 'Selected brand' : 'Select brand'; ?>
                                </span>
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>

                            <div class="custom-select-menu" data-custom-select-menu>
                                <button type="button" class="custom-select-option is-placeholder" data-value="">
                                    Select brand
                                </button>

                                <?php if ($query_brand) : ?>
                                    <?php while ($brand = mysqli_fetch_assoc($query_brand)) : ?>
                                        <button
                                            type="button"
                                            class="custom-select-option"
                                            data-value="<?= (int) $brand['brand_id']; ?>"
                                            data-label="<?= htmlspecialchars($brand['name'], ENT_QUOTES, 'UTF-8'); ?>">
                                            <?= htmlspecialchars($brand['name'], ENT_QUOTES, 'UTF-8'); ?>
                                        </button>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="price" class="form-label">Price</label>
                        <input
                            type="text"
                            name="price"
                            id="price"
                            class="form-control"
                            value="<?= htmlspecialchars($price, ENT_QUOTES, 'UTF-8'); ?>"
                            placeholder="Enter price"
                            required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="name" class="form-label">Name</label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        class="form-control"
                        value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>"
                        placeholder="Enter product name"
                        required>
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Description</label>
                    <textarea
                        name="description"
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
                                accept="image/png,image/jpeg,image/jpg,image/webp"
                                required>

                            <div class="file-dropzone-inner">
                                <i class="fa-regular fa-image"></i>
                                <div class="file-dropzone-title">Drag image here or click to upload</div>
                                <div class="file-dropzone-hint">PNG, JPG, JPEG, WEBP</div>
                                <div class="file-dropzone-btn">Choose File</div>
                            </div>
                        </div>

                        <div class="form-helper">Gambar akan tampil preview setelah file dipilih.</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Preview</label>
                        <div class="image-preview is-hidden" id="imagePreview">
                            <img id="imagePreviewImg" src="" alt="Image Preview">
                        </div>
                    </div>
                </div>

                <div class="action-group">
                    <button type="submit" name="save" class="btn-add">
                        Add Product
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
        const hiddenInput = customSelect.querySelector('#brand_id');
        const label = customSelect.querySelector('[data-custom-select-label]');
        const options = customSelect.querySelectorAll('[data-custom-select-menu] .custom-select-option');

        const closeMenu = () => customSelect.classList.remove('open');
        const openMenu = () => customSelect.classList.add('open');
        const toggleMenu = () => customSelect.classList.toggle('open');

        trigger.addEventListener('click', () => {
            toggleMenu();
        });

        options.forEach((option) => {
            option.addEventListener('click', () => {
                const value = option.dataset.value || '';
                const text = option.dataset.label || option.textContent.trim();

                hiddenInput.value = value;
                label.textContent = value === '' ? 'Select brand' : text;

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
    })();
    (() => {
        const fileInput = document.getElementById('image_file');
        const dropzone = document.getElementById('fileDropzone');
        const previewBox = document.getElementById('imagePreview');
        const previewImg = document.getElementById('imagePreviewImg');

        if (!fileInput || !dropzone || !previewBox || !previewImg) return;

        const showPreview = (file) => {
            if (!file) {
                previewImg.removeAttribute('src');
                previewBox.classList.add('is-hidden');
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                previewImg.src = e.target.result;
                previewBox.classList.remove('is-hidden');
            };
            reader.readAsDataURL(file);
        };

        fileInput.addEventListener('change', function() {
            const file = this.files && this.files[0] ? this.files[0] : null;
            showPreview(file);
        });

        ['dragenter', 'dragover'].forEach((eventName) => {
            dropzone.addEventListener(eventName, (event) => {
                event.preventDefault();
                event.stopPropagation();
                dropzone.classList.add('dragover');
            });
        });

        ['dragleave', 'drop'].forEach((eventName) => {
            dropzone.addEventListener(eventName, (event) => {
                event.preventDefault();
                event.stopPropagation();
                dropzone.classList.remove('dragover');
            });
        });

        dropzone.addEventListener('drop', (event) => {
            const files = event.dataTransfer.files;
            if (!files || !files.length) return;

            fileInput.files = files;
            showPreview(files[0]);
        });
    })();
</script>