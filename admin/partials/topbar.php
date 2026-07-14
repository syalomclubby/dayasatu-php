<?php
require_once __DIR__ . "/../../config/connection.php";
?>
<header class="topbar">

    <div class="topbar-left">
        <button
            class="menu-toggle"
            id="menuToggle"
            type="button"
            aria-label="Open menu">
            <i class="fa-solid fa-bars"></i>
        </button>

        <div class="topbar-title">
            <?= $current_menu === 'dashboard' ? '<h2> Dashboard </h2>' : '' ?>
            <?= $current_menu === 'products' ? '<h2> Products </h2>' : '' ?>
            <?= $current_menu === 'brands' ? '<h2> Brands </h2>' : '' ?>
            <?= $current_menu === 'categories' ? '<h2> Categories </h2>' : '' ?>
            <?= $current_menu === 'users' ? '<h2> Users </h2>' : '' ?>
            <p>Overview of your system</p>
        </div>
    </div>

    <div class="topbar-right">
        <div class="notif-wrapper" id="notifWrapper" data-url="<?= $base_url ?>admin/partials/sidebar.php?action=get_all_activities">
            <button class="icon-button" id="notifBtn">
                <i class="fa-regular fa-bell"></i>
            </button>

            <div class="notif-panel" id="notifPanel">
                <div class="notif-header">
                    Activity
                </div>
                <div class="notif-list" id="notifList">
                </div>
                <div class="notif-footer">
                    <button type="button" id="toggleNotifBtn" class="notif-toggle-btn">
                        See More
                        <i class="fa-solid fa-chevron-down"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="admin-pill">
            <?= htmlspecialchars($_SESSION['username']) ?>
        </div>
    </div>

</header>