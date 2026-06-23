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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

            <?php
            $current_page = basename($_SERVER['PHP_SELF']);

            $current_menu = '';

            if (in_array($current_page, [
                'products.php',
                'create-product.php',
                'edit-product.php'
            ])) {
                $current_menu = 'products';
            } elseif (in_array($current_page, [
                'brands.php',
                'create-brand.php',
                'edit-brand.php'
            ])) {
                $current_menu = 'brands';
            } elseif (in_array($current_page, [
                'categories.php',
                'create-category.php',
                'edit-category.php'
            ])) {
                $current_menu = 'categories';
            } elseif ($current_page === 'dashboard.php') {
                $current_menu = 'dashboard';
            } elseif ($current_page === 'settings.php') {
                $current_menu = 'settings';
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

                <a href="<?= $base_url ?>index.php" class="<?= $current_menu === 'settings' ? 'active' : '' ?>">
                    <i class="fa-solid fa-gear"></i>
                    <span>Beranda</span>
                </a>

                <div class="sidebar-footer">
                    <a href="<?= $base_url ?>admin/logout.php" class="btn btn-danger">
                        Logout <i class="fa-solid fa-right-from-bracket"></i>
                    </a>
                </div>
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
                </div>
            </header>