<?php
require_once __DIR__ . "/../security.php";
require_once __DIR__ . "/../../config/connection.php";

$search = trim($_GET['search'] ?? '');

$where = '';

if ($search !== '') {

    $search = mysqli_real_escape_string($conn, $search);

    $where = "
        WHERE
            brands.name LIKE '%$search%'
            OR users.name LIKE '%$search%'
    ";
}

$sql_brand = "
SELECT *,
brands.name AS brand_name,
brands.followed_up_at AS brands_at,
brands.created_at AS waktu,
users.name AS user_name
FROM brands
INNER JOIN users
ON brands.followed_up_by = users.user_id
$where
ORDER BY brands.name DESC
";

$query_brand = mysqli_query($conn, $sql_brand);

function renderBrandRows(mysqli_result $query_brand): string
{
    $brand = 1;

    ob_start();

    while ($result = mysqli_fetch_assoc($query_brand)) {

        $brand_id = $result['brand_id'];

?>

        <tr>

            <td>
                <span class="table-id">#<?= $brand++; ?></span>
            </td>

            <td>
                <div class="table-name">
                    <?= htmlspecialchars($result['brand_name']); ?>
                </div>
            </td>

            <td class="table-muted">
                <?= $result['user_name']; ?>
            </td>

            <td class="table-muted">
                <?= $result['brands_at'] ?? '-'; ?>
            </td>

            <td class="table-muted">
                <?= $result['waktu']; ?>
            </td>

            <td>

                <div class="table-actions">

                    <a
                        href="edit/edit_brand.php?brand_id=<?= $brand_id; ?>"
                        class="table-action">

                        <i class="fa-solid fa-pen"></i>

                    </a>

                    <a
                        href="delete/delete_brand.php?brand_id=<?= $brand_id; ?>"
                        class="table-action table-action-danger btn-delete-brand">

                        <i class="fa-solid fa-trash"></i>

                    </a>

                </div>

            </td>

        </tr>

    <?php

    }

    return ob_get_clean();
}

if (isset($_GET['ajax'])) {

    header('Content-Type: application/json');

    ob_start();

    if (mysqli_num_rows($query_brand) > 0) {

    ?>

        <div class="table-responsive table-card">

            <table class="table">

                <thead>

                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Followed Up By</th>
                        <th>Followed Up At</th>
                        <th>Created At</th>
                        <th class="table-actions-head"></th>
                    </tr>

                </thead>

                <tbody>

                    <?= renderBrandRows($query_brand); ?>

                </tbody>

            </table>

        </div>

    <?php

    } else {

    ?>

        <div class="empty-state">

            <div class="empty-state-icon">
                <i class="fa-solid fa-tags"></i>
            </div>

            <h3>No Brands Found</h3>

            <p>
                We couldn't find any brands matching your search.
            </p>

        </div>

<?php

    }

    echo json_encode([
        'table' => ob_get_clean()
    ]);

    exit;
}

?>

<?php include '../partials/sidebar.php' ?>

<div class="content">

    <?php if (isset($_GET['success'])) : ?>

        <?php if ($_GET['success'] === 'added') : ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i>
                <span>Brand successfully added.</span>
            </div>
        <?php endif; ?>

        <?php if ($_GET['success'] === 'updated') : ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i>
                <span>Brand successfully updated.</span>
            </div>
        <?php endif; ?>

    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'brand_has_products') : ?>
        <div class="alert alert-danger">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>
                The brand cannot be removed because it still has products.
            </span>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['success']) && $_GET['success'] === 'deleted') : ?>
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check"></i>
            <span>
                Brand berhasil dihapus.
            </span>
        </div>
    <?php endif; ?>

    <div class="page-header">
        <a href="../dashboard.php" class="btn-back">
            <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
        </a>

        <a href="add/add_brand.php" class="btn-add">
            Add Brands <i class="fa-solid fa-plus"></i>
        </a>
    </div>

    <div class="table-toolbar">
        <form class="table-search">

            <div class="search-box">

                <i class="fa-solid fa-magnifying-glass"></i>

                <input
                    id="searchInput"
                    type="text"
                    autocomplete="off"
                    placeholder="Search brand..."
                    value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">

                <a
                    id="searchClear"
                    class="search-clear"
                    style="<?= empty($_GET['search']) ? 'display:none' : '' ?>">

                    <i class="fa-solid fa-xmark"></i>

                </a>

            </div>

        </form>
    </div>

    <div id="tableContainer">
        <div class="table-responsive table-card">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Followed Up By</th>
                        <th>Followed Up At</th>
                        <th>Created At</th>
                        <th class="table-actions-head"></th>
                    </tr>
                </thead>
                <tbody>

                    <?= renderBrandRows($query_brand); ?>

                </tbody>
            </table>
        </div>
    </div>
</div>