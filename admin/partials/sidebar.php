<?php require_once __DIR__ . "/../../config/connection.php"; ?>

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
</head>

<body>

    <div class="admin-shell">

        <!-- SIDEBAR -->
        <aside class="sidebar">
            <a href="<?= $base_url ?>admin/dashboard.php" class="brand">
                <img src="<?= $base_url ?>assets/images/logo.png" alt="Logo">

                <div>
                    <h1>CV Daya Satu</h1>
                    <p>Admin Dashboard</p>
                </div>
            </a>

            <div class="menu-title">
                Navigation
            </div>

            <nav class="menu">
                <a href="<?= $base_url ?>admin/dashboard.php" class="active">
                    <i class="fa-solid fa-house"></i>
                    <span>Dashboard</span>
                </a>

                <a href="<?= $base_url ?>admin/product/products.php">
                    <i class="fa-solid fa-box"></i>
                    <span>Products</span>
                </a>

                <a href="<?= $base_url ?>admin/product/brands.php">
                    <i class="fa-solid fa-tags"></i>
                    <span>Brands</span>
                </a>

                <a href="<?= $base_url ?>admin/product/categories.php">
                    <i class="fa-solid fa-layer-group"></i>
                    <span>Categories</span>
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