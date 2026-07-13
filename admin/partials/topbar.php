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
            <h2>Dashboard</h2>
            <p>Overview of your system</p>
        </div>
    </div>

    <div class="topbar-right">
        <div class="notif-wrapper" id="notifWrapper">
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
            <?= htmlspecialchars($_SESSION['name']) ?>
        </div>
    </div>

    <script>
        (function() {

            const wrapper = document.getElementById('notifWrapper');
            const btn = document.getElementById('notifBtn');
            const panel = document.getElementById('notifPanel');
            const list = document.getElementById('notifList');
            const toggleBtn = document.getElementById('toggleNotifBtn');

            let isOpen = false;
            let loaded = false;
            let expanded = false;

            function applyCollapsedState() {

                const items = list.querySelectorAll('.notif-item');

                items.forEach((item, index) => {

                    item.style.display = index < 5 ? 'flex' : 'none';

                });

                expanded = false;

                toggleBtn.innerHTML = `
            See More
            <i class="fa-solid fa-chevron-down"></i>
        `;
            }

            function fetchNotifications() {

                list.innerHTML =
                    '<div class="notif-loading" style="text-align:center;padding:20px;color:var(--text-light);"><i class="fa-solid fa-spinner fa-spin"></i> Loading...</div>';

                fetch('<?= $base_url ?>admin/partials/sidebar.php?action=get_all_activities')

                    .then(res => {

                        if (!res.ok) {
                            throw new Error();
                        }

                        return res.text();

                    })

                    .then(html => {

                        list.innerHTML = html;

                        loaded = true;

                        const items = list.querySelectorAll('.notif-item');

                        if (items.length > 5) {

                            toggleBtn.style.display = 'flex';

                            applyCollapsedState();

                        } else {

                            toggleBtn.style.display = 'none';

                        }

                    })

                    .catch(() => {

                        list.innerHTML =
                            '<div class="notif-empty" style="text-align:center;padding:20px;color:var(--danger);"><i class="fa-solid fa-circle-exclamation"></i> Failed to load.</div>';

                    });

            }

            toggleBtn.addEventListener("click", function() {

                const items = list.querySelectorAll(".notif-item");

                if (!expanded) {

                    items.forEach(item => {

                        item.style.display = "flex";

                    });

                    expanded = true;

                    toggleBtn.innerHTML = `
                Show Less
                <i class="fa-solid fa-chevron-up"></i>
            `;

                } else {

                    applyCollapsedState();

                }

            });

            function togglePanel() {

                if (isOpen) {

                    panel.style.display = 'none';

                    btn.classList.remove('active');

                    isOpen = false;

                    if (loaded) {
                        applyCollapsedState();
                    }

                } else {

                    panel.style.display = 'block';

                    btn.classList.add('active');

                    isOpen = true;

                    if (!loaded) {

                        fetchNotifications();

                    } else {

                        applyCollapsedState();

                    }

                }

            }

            btn.addEventListener('click', function(e) {

                e.stopPropagation();

                togglePanel();

            });

            document.addEventListener('click', function(e) {

                if (isOpen && !wrapper.contains(e.target)) {

                    panel.style.display = 'none';

                    btn.classList.remove('active');

                    isOpen = false;

                    if (loaded) {
                        applyCollapsedState();
                    }

                }

            });

            document.addEventListener('keydown', function(e) {

                if (e.key === "Escape" && isOpen) {

                    panel.style.display = 'none';

                    btn.classList.remove('active');

                    isOpen = false;

                    if (loaded) {
                        applyCollapsedState();
                    }

                }

            });

        })();
    </script>
</header>