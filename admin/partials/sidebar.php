<?php
require_once __DIR__ . "/../../config/connection.php";
require_once __DIR__ . "/../security.php";

$logged_in_id = $_SESSION['user_id'] ?? 0;

$sql_check_role = "SELECT role FROM users WHERE user_id = ? LIMIT 1";
$stmt_check = mysqli_prepare($conn, $sql_check_role);
$is_superadmin = false;

if ($stmt_check) {
    mysqli_stmt_bind_param($stmt_check, "i", $logged_in_id);
    mysqli_stmt_execute($stmt_check);
    $res_check = mysqli_stmt_get_result($stmt_check);
    $data_check = mysqli_fetch_assoc($res_check);

    if (isset($data_check['role']) && $data_check['role'] == 1) {
        $is_superadmin = true;
    }
    mysqli_stmt_close($stmt_check);
}

if (isset($_GET['action']) && $_GET['action'] === 'get_all_activities') {
    
    $sql_activity = "SELECT
        activity_type, item_id, item_name, meta_text, performed_by, activity_at, activity_label
    FROM (
        SELECT 'product' AS activity_type, p.product_id AS item_id, p.product_name AS item_name,
            CONCAT(b.brand_name, ' • ', c.category_name) AS meta_text, u.username AS performed_by,
            COALESCE(p.followed_up_at, p.created_at) AS activity_at,
            CASE WHEN p.followed_up_at IS NULL THEN 'Product Added' ELSE 'Product Updated' END AS activity_label
        FROM products p
        LEFT JOIN users u ON p.followed_up_by = u.user_id
        INNER JOIN brands b ON p.brand_id = b.brand_id
        INNER JOIN categories c ON p.category_id = c.category_id

        UNION ALL

        SELECT 'brand' AS activity_type, b.brand_id AS item_id, b.brand_name AS item_name, 'Brand' AS meta_text,
            u.username AS performed_by, COALESCE(b.followed_up_at, b.created_at) AS activity_at,
            CASE WHEN b.followed_up_at IS NULL THEN 'Brand Added' ELSE 'Brand Updated' END AS activity_label
        FROM brands b LEFT JOIN users u ON b.followed_up_by = u.user_id

        UNION ALL

        SELECT 'category' AS activity_type, c.category_id AS item_id, c.category_name AS item_name, 'Category' AS meta_text,
            u.username AS performed_by, COALESCE(c.followed_up_at, c.created_at) AS activity_at,
            CASE WHEN c.followed_up_at IS NULL THEN 'Category Added' ELSE 'Category Updated' END AS activity_label
        FROM categories c LEFT JOIN users u ON c.followed_up_by = u.user_id
    ) AS all_activity 
    WHERE activity_at IS NOT NULL 
    ORDER BY activity_at DESC";

    $query_activity = mysqli_query($conn, $sql_activity);

    if ($query_activity && mysqli_num_rows($query_activity) > 0) {
        while ($row = mysqli_fetch_assoc($query_activity)) {
            $icon_class = 'fa-box';
            $bg_icon_class = '';
            if ($row['activity_type'] == 'brand') {
                $icon_class = 'fa-tags';
                $bg_icon_class = 'activity-brand';
            } elseif ($row['activity_type'] == 'category') {
                $icon_class = 'fa-layer-group';
                $bg_icon_class = 'activity-category';
            }

            $status_class = 'created';
            if (stripos($row['activity_label'], 'Updated') !== false) {
                $status_class = 'updated';
            } elseif (stripos($row['activity_label'], 'Deleted') !== false) {
                $status_class = 'deleted';
            }
?>
            <div class="notif-item" style="padding: 12px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 12px;">
                <div class="activity-icon <?= $bg_icon_class ?>" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 8px; flex-shrink:0;">
                    <i class="fa-solid <?= $icon_class ?>"></i>
                </div>
                <div class="notif-item-body" style="flex: 1; min-width: 0; text-align: left;">
                    <div class="notif-item-label" style="font-size: 11px; font-weight:600; color:var(--text-light); text-transform:uppercase;"><?= htmlspecialchars($row['activity_label']) ?></div>
                    <div class="notif-item-name" style="font-size: 14px; font-weight:600; color:var(--text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?= htmlspecialchars($row['item_name']) ?></div>
                    <div class="notif-item-meta" style="font-size: 12px; color:var(--text-light); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        <?= htmlspecialchars($row['meta_text']) ?>
                        <?= !empty($row['performed_by']) ? '• ' . htmlspecialchars($row['performed_by']) : '' ?>
                    </div>
                </div>
                <div class="activity-date <?= $status_class ?>" style="font-size: 10px; flex-shrink:0; text-align:right;">
                    <span><?= date('d M', strtotime($row['activity_at'])); ?></span>
                    <br>
                    <span><?= date('H:i', strtotime($row['activity_at'])); ?></span>
                </div>
            </div>
<?php
        }
    } else {
        echo '<div class="notif-empty" style="text-align:center; padding:20px; color:var(--text-light);"><i class="fa-solid fa-bell-slash"></i> <span>Tidak ada aktivitas terbaru</span></div>';
    }
    exit;
}

$logged_in_id = $_SESSION['user_id'] ?? 0;

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CV Daya Satu</title>

    <link rel="icon" href="<?= $base_url ?>assets/images/logo.ico">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <link rel="stylesheet" href="<?= $base_url ?>assets/css/admin.css">
    <script src="<?= $base_url ?>assets/js/admin.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <div class="admin-shell">

        <!-- SIDEBAR -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="<?= $base_url ?>admin/dashboard.php" class="brand">
                    <img src="<?= $base_url ?>assets/images/logo.png" alt="Logo">

                    <div>
                        <h1>CV Daya Satu</h1>
                        <p>Admin Dashboard</p>
                    </div>
                </a>

                <button
                    type="button"
                    class="sidebar-close"
                    id="sidebarClose"
                    aria-label="Close sidebar">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="menu-title">
                Navigation
            </div>

            <?php
            $current_page = basename($_SERVER['PHP_SELF']);

            $current_menu = '';

            if (in_array($current_page, [
                'dashboard.php',
            ])) {
                $current_menu = 'dashboard';
            } elseif (in_array($current_page, [
                'products.php',
                'add_product.php',
                'edit_product.php',
                'delete_product.php',
                'product_prices.php',
                'add_product_price.php',
                'edit_product_price.php',
            ])) {
                $current_menu = 'products';
            } elseif (in_array($current_page, [
                'brands.php',
                'add_brand.php',
                'edit_brand.php',
                'delete_brand.php',
            ])) {
                $current_menu = 'brands';
            } elseif (in_array($current_page, [
                'categories.php',
                'add_category.php',
                'edit_category.php',
                'delete_category.php',
            ])) {
                $current_menu = 'categories';
            } elseif (in_array($current_page, [
                'users.php',
                'add_user.php',
                'edit_user.php',
                'delete_user.php',
            ])) {
                $current_menu = 'users';
            } else {
                $current_menu = '';
            }
            ?>

            <nav class="menu">
                <a href="<?= $base_url ?>admin/dashboard.php" class="<?= $current_menu === 'dashboard' ? 'active' : '' ?>">
                    <i class="fa-solid fa-house"></i>
                    <span>Dashboard</span>
                </a>

                <a href="<?= $base_url ?>admin/product/products.php" class="<?= $current_menu === 'products' ? 'active' : '' ?>">
                    <i class="fa-solid fa-box"></i>
                    <span>Products</span>
                </a>

                <a href="<?= $base_url ?>admin/product/brands.php" class="<?= $current_menu === 'brands' ? 'active' : '' ?>">
                    <i class="fa-solid fa-tags"></i>
                    <span>Brands</span>
                </a>

                <a href="<?= $base_url ?>admin/product/categories.php" class="<?= $current_menu === 'categories' ? 'active' : '' ?>">
                    <i class="fa-solid fa-layer-group"></i>
                    <span>Categories</span>
                </a>


                <?php if ($is_superadmin) : ?>
                    <a href="<?= $base_url ?>admin/user/users.php" class="<?= $current_menu === 'users' ? 'active' : '' ?>">
                        <i class="fa-solid fa-user"></i>
                        <span>User</span>
                    </a>
                <?php endif; ?>


                <a href="<?= $base_url ?>index.php" class="visit-site-link" target="_blank" rel="noopener">
                    <span class="visit-site-icon">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </span>
                    <span>Visit Site</span>
                </a>

                <div class="sidebar-footer">
                    <a href="<?= $base_url ?>admin/logout.php" class="btn btn-danger">
                        Logout <i class="fa-solid fa-right-from-bracket"></i>
                    </a>
                </div>
            </nav>
        </aside>

        <!-- MAIN -->
        <main class="main" id="main">

            <!-- TOPBAR -->
            <?php require_once __DIR__ . "/topbar.php"; ?>