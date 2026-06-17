<?php
require_once __DIR__ . "/security.php";
require_once __DIR__ . "/../config/connection.php";

$page_title = "Dashboard";

// Statistik
$total_products = 0;
$total_brands = 0;
$total_categories = 0;
$total_users = 0;

// Ambil Data
$sql_category = "SELECT * FROM categories";
$query_category = mysqli_query($conn, $sql_category);

$sql_brand = "SELECT *,
categories.name AS category_name,
brands.name AS brand_name
FROM brands
INNER JOIN categories ON brands.category_id = categories.category_id";
$query_brand = mysqli_query($conn, $sql_brand);

$sql_products = "SELECT *,
products.name AS product_name,
brands.name AS brand_name,
categories.name AS category_name
FROM products
INNER JOIN brands ON products.brand_id = brands.brand_id
INNER JOIN categories ON brands.category_id = categories.category_id
ORDER BY products.product_id DESC
LIMIT 5";
$query_product = mysqli_query($conn, $sql_products);

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - CV Daya Satu</title>

    <link rel="icon" href="../assets/images/logo.ico">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body>

    <div class="admin-shell">

        <!-- SIDEBAR -->
        <aside class="sidebar">
            <a href="dashboard.php" class="brand">
                <img src="../assets/images/logo.png" alt="Logo">

                <div>
                    <h1>CV Daya Satu</h1>
                    <p>Admin Dashboard</p>
                </div>
            </a>

            <div class="menu-title">
                Navigation
            </div>

            <nav class="menu">
                <a href="dashboard.php" class="active">
                    <i class="fa-solid fa-grid-2"></i>
                    <span>Dashboard</span>
                </a>

                <a href="product/products.php">
                    <i class="fa-solid fa-box"></i>
                    <span>Products</span>
                </a>

                <a href="product/brands.php">
                    <i class="fa-solid fa-tags"></i>
                    <span>Brands</span>
                </a>

                <a href="product/categories.php">
                    <i class="fa-solid fa-layer-group"></i>
                    <span>Categories</span>
                </a>

                <a href="users.php">
                    <i class="fa-solid fa-users"></i>
                    <span>Users</span>
                </a>

                <a href="settings.php">
                    <i class="fa-solid fa-gear"></i>
                    <span>Settings</span>
                </a>
            </nav>
        </aside>

        <!-- MAIN -->
        <main class="main">

            <!-- TOPBAR -->
            <header class="topbar">
                <div class="topbar-left">
                    <h2>Dashboard</h2>

                    <p>
                        Overview of your system
                    </p>
                </div>

                <div class="topbar-right">
                    <button class="icon-button">
                        <i class="fa-regular fa-bell"></i>
                    </button>

                    <div class="admin-pill">
                        <?= htmlspecialchars($_SESSION['name']) ?>
                    </div>

                    <a href="logout.php" class="btn btn-danger">
                        Logout
                    </a>
                </div>
            </header>

            <!-- CONTENT -->
            <section class="content">

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
                                <h3 class="welcome-title">Welcome back, <?= ($_SESSION['name'] ?? 'Admin') ?>.</h3>
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
                                            <img
                                                src="../assets/images/products/<?= $row['image'] ?>"
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

                                        <a href="product/edit_product.php?id=<?= $row['product_id'] ?>" class="product-action">
                                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                        </a>

                                    </div>

                                <?php endwhile; ?>

                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div>
                                <div class="card-title">Quick Actions</div>
                                <div class="card-subtitle">Main tasks for daily operations</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="quick-actions">
                                <a class="quick-action" href="product/add_product.php">
                                    <div class="quick-action-left">
                                        <div class="quick-action-icon"><i class="fa-solid fa-plus"></i></div>
                                        <div>
                                            <div class="quick-action-title">Add Product</div>
                                            <div class="quick-action-desc">Insert a new product record</div>
                                        </div>
                                    </div>
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                                <a class="quick-action" href="product/add_brand.php">
                                    <div class="quick-action-left">
                                        <div class="quick-action-icon"><i class="fa-solid fa-tags"></i></div>
                                        <div>
                                            <div class="quick-action-title">Add Brand</div>
                                            <div class="quick-action-desc">Register a new brand entry</div>
                                        </div>
                                    </div>
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                                <a class="quick-action" href="product/add_category.php">
                                    <div class="quick-action-left">
                                        <div class="quick-action-icon"><i class="fa-solid fa-layer-group"></i></div>
                                        <div>
                                            <div class="quick-action-title">Add Category</div>
                                            <div class="quick-action-desc">Create a new product group</div>
                                        </div>
                                    </div>
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                                <a class="quick-action" href="product/products.php">
                                    <div class="quick-action-left">
                                        <div class="quick-action-icon"><i class="fa-solid fa-box"></i></div>
                                        <div>
                                            <div class="quick-action-title">View Products</div>
                                            <div class="quick-action-desc">Open product management</div>
                                        </div>
                                    </div>
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                                <a class="quick-action" href="product/brands.php">
                                    <div class="quick-action-left">
                                        <div class="quick-action-icon"><i class="fa-solid fa-tags"></i></div>
                                        <div>
                                            <div class="quick-action-title">View Brands</div>
                                            <div class="quick-action-desc">Open brand management</div>
                                        </div>
                                    </div>
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                                <a class="quick-action" href="product/categories.php">
                                    <div class="quick-action-left">
                                        <div class="quick-action-icon"><i class="fa-solid fa-layer-group"></i></div>
                                        <div>
                                            <div class="quick-action-title">View Categories</div>
                                            <div class="quick-action-desc">Open category management</div>
                                        </div>
                                    </div>
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>


</body>

</html>