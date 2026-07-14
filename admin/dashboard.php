<?php
require_once __DIR__ . "/security.php";
require_once __DIR__ . "/../config/connection.php";

$page_title = "Dashboard";


//Statistik
$sql = "SELECT 
            (SELECT COUNT(*) FROM products) AS t_products,
            (SELECT COUNT(*) FROM brands) AS t_brands,
            (SELECT COUNT(*) FROM categories) AS t_categories,
            (SELECT COUNT(*) FROM users) AS t_users";

$result = $conn->query($sql);
$data = $result->fetch_assoc();

$total_products   = $data['t_products'] ?? 0;
$total_brands     = $data['t_brands'] ?? 0;
$total_categories = $data['t_categories'] ?? 0;
$total_users      = $data['t_users'] ?? 0;

// Ambil Data
$sql_category = "SELECT * FROM categories";
$query_category = mysqli_query($conn, $sql_category);

$sql_brand = "SELECT *
FROM brands";
$query_brand = mysqli_query($conn, $sql_brand);

$sql_products = "SELECT
products.*,
products.product_name AS product_name,
brands.brand_name AS brand_name,
categories.category_name AS category_name
FROM products
INNER JOIN brands
ON products.brand_id = brands.brand_id
INNER JOIN categories
ON products.category_id = categories.category_id
ORDER BY products.product_id DESC
LIMIT 5";
$query_product = mysqli_query($conn, $sql_products);

// Recent Activity
$sql_activity = "SELECT
    activity_type,
    item_id,
    item_name,
    meta_text,
    performed_by,
    activity_at,
    activity_label
FROM (
    SELECT
        'product' AS activity_type,
        p.product_id AS item_id,
        p.product_name AS item_name,
        CONCAT(b.brand_name, ' • ', c.category_name) AS meta_text,
        u.username AS performed_by,
        COALESCE(p.followed_up_at, p.created_at) AS activity_at,
        CASE
            WHEN p.followed_up_at IS NULL THEN 'Product Added'
            ELSE 'Product Updated'
        END AS activity_label
    FROM products p
    LEFT JOIN users u ON p.followed_up_by = u.user_id
    INNER JOIN brands b ON p.brand_id = b.brand_id
    INNER JOIN categories c ON p.category_id = c.category_id

    UNION ALL

    SELECT
        'brand' AS activity_type,
        b.brand_id AS item_id,
        b.brand_name AS item_name,
        'Brand' AS meta_text,
        u.username AS performed_by,
        COALESCE(b.followed_up_at, b.created_at) AS activity_at,
        CASE
            WHEN b.followed_up_at IS NULL THEN 'Brand Added'
            ELSE 'Brand Updated'
        END AS activity_label
    FROM brands b
    LEFT JOIN users u ON b.followed_up_by = u.user_id

    UNION ALL

    SELECT
        'category' AS activity_type,
        c.category_id AS item_id,
        c.category_name AS item_name,
        'Category' AS meta_text,
        u.username AS performed_by,
        COALESCE(c.followed_up_at, c.created_at) AS activity_at,
        CASE
            WHEN c.followed_up_at IS NULL THEN 'Category Added'
            ELSE 'Category Updated'
        END AS activity_label
    FROM categories c
    LEFT JOIN users u ON c.followed_up_by = u.user_id
) AS all_activity
WHERE activity_at IS NOT NULL
ORDER BY activity_at DESC
LIMIT 5";

$query_activity = mysqli_query($conn, $sql_activity);

?>

<?php include 'partials/sidebar.php' ?>

<!-- CONTENT -->
<section class="content">

    <div id="mobile-banner-slot"></div>

    <!-- STATS -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-label">
                    Products
                </div>

                <div class="stat-value">
                    <?= $total_products ?>
                </div>
            </div>

            <div class="stat-icon stat-orange">
                <i class="fa-solid fa-box"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-label">
                    Brands
                </div>

                <div class="stat-value">
                    <?= $total_brands ?>
                </div>
            </div>

            <div class="stat-icon stat-blue">
                <i class="fa-solid fa-tags"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-label">
                    Categories
                </div>

                <div class="stat-value">
                    <?= $total_categories ?>
                </div>
            </div>

            <div class="stat-icon stat-green">
                <i class="fa-solid fa-layer-group"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-label">
                    Users
                </div>

                <div class="stat-value">
                    <?= $total_users ?>
                </div>
            </div>

            <div class="stat-icon stat-purple">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>

    </div>

    <!-- QUICK ACTIONS -->
    <div class="grid-2 mt-4">
        <div>
            <div class="card welcome-banner">
                <div class="card-body">
                    <div class="welcome-top">
                        <span class="banner-badge"><i class="fa-solid fa-sparkles"></i> Admin overview</span>
                        <span class="banner-date"><?= date("l, d M Y") ?></span>
                    </div>
                    <h3 class="welcome-title">Welcome back, <?= ($_SESSION['username'] ?? 'Admin') ?>.</h3>
                    <p class="welcome-text">Monitor products, brands, categories, and system activity from one dashboard.</p>
                </div>
            </div>

            <div class="card latest-products-card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Latest Products</div>
                        <div class="card-subtitle">Recently added products</div>
                    </div>
                    <a href="product/products.php" class="view-all-link">
                        View All
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>

                <div class="latest-products-list">

                    <?php while ($row = mysqli_fetch_assoc($query_product)): ?>

                        <div class="latest-product-item">

                            <div class="product-image">
                                <?php
                                $brand_folder = strtolower(trim($row['brand_name']));
                                ?>
                                <img
                                    src="../assets/images/products/<?= ($brand_folder) ?>/<?= $row['product_image'] ?>.png"
                                    alt="<?= $row['product_name'] ?>">
                            </div>

                            <div class="product-info">
                                <div class="products-name">
                                    <?= ($row['product_name']) ?>
                                </div>

                                <div class="product-meta">
                                    <?= ($row['brand_name']) ?>
                                    <span>•</span>
                                    <?= ($row['category_name']) ?>
                                </div>
                            </div>

                            <a href="product/edit/edit_product.php?product_id=<?= $row['product_id']; ?>" class="table-action" aria-label="Edit">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            </a>

                        </div>

                    <?php endwhile; ?>

                </div>
            </div>
        </div>

        <div class="card quick-actions-card">
            <div class="card-header">
                <div>
                    <div class="card-title">Quick Actions</div>
                    <div class="card-subtitle">Main tasks for daily operations</div>
                </div>
            </div>
            <div class="card-body">
                <div class="quick-actions">
                    <a class="quick-action" href="product/add/add_product.php">
                        <div class="quick-action-left">
                            <div class="quick-action-icon"><i class="fa-solid fa-plus"></i></div>
                            <div>
                                <div class="quick-action-title">Add Product</div>
                                <div class="quick-action-desc">Insert a new product record</div>
                            </div>
                        </div>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                    <a class="quick-action" href="product/add/add_brand.php">
                        <div class="quick-action-left">
                            <div class="quick-action-icon"><i class="fa-solid fa-tags"></i></div>
                            <div>
                                <div class="quick-action-title">Add Brand</div>
                                <div class="quick-action-desc">Register a new brand entry</div>
                            </div>
                        </div>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                    <a class="quick-action" href="product/add/add_category.php">
                        <div class="quick-action-left">
                            <div class="quick-action-icon"><i class="fa-solid fa-layer-group"></i></div>
                            <div>
                                <div class="quick-action-title">Add Category</div>
                                <div class="quick-action-desc">Create a new product group</div>
                            </div>
                        </div>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php mysqli_data_seek($query_product, 0); ?>

    <div class="grid-2 mt-4">
        <div class="card activity-card">
            <div class="card-header">
                <div>
                    <div class="card-title">Recent Activity</div>
                    <div class="card-subtitle">Latest product updates</div>
                </div>
            </div>

            <div class="activity-feed">

                <?php while ($row = mysqli_fetch_assoc($query_activity)): ?>

                    <?php
                    $icon_map = [
                        'product'  => 'fa-box',
                        'brand'    => 'fa-tags',
                        'category' => 'fa-layer-group',
                    ];

                    $activity_class = match ($row['activity_type']) {
                        'product'  => 'activity-product',
                        'brand'    => 'activity-brand',
                        'category' => 'activity-category',
                        'user'     => 'activity-user',
                        default    => 'activity-product'
                    };

                    $icon = $icon_map[$row['activity_type']] ?? 'fa-box';
                    ?>

                    <div class="activity-row">
                        <div class="activity-icon <?= $activity_class ?>">
                            <i class="fa-solid <?= $icon ?>"></i>
                        </div>

                        <div class="activity-content">
                            <div class="activity-label">
                                <?= htmlspecialchars($row['activity_label']) ?>
                            </div>

                            <div class="activity-title">
                                <?= htmlspecialchars($row['item_name']) ?>
                            </div>

                            <div class="activity-meta">
                                <?= htmlspecialchars($row['meta_text']) ?>
                                <?php if (!empty($row['performed_by'])): ?>
                                    • <?= htmlspecialchars($row['performed_by']) ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php

                        $status_class = 'created';

                        if (stripos($row['activity_label'], 'Updated') !== false) {
                            $status_class = 'updated';
                        }

                        if (stripos($row['activity_label'], 'Deleted') !== false) {
                            $status_class = 'deleted';
                        }

                        ?>

                        <div class="activity-date <?= $status_class ?>">
                            <span><?= date('d M Y', strtotime($row['activity_at'])); ?></span>
                            <span><?= date('H:i', strtotime($row['activity_at'])); ?></span>
                        </div>
                    </div>

                <?php endwhile; ?>

            </div>
        </div>
    </div>
</section>
</main>
</div>

</div>

</body>

</html>