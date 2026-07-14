<?php

require_once __DIR__ . "/../security.php";
require_once __DIR__ . "/../../config/connection.php";

$search = trim($_GET['search'] ?? '');

$where = '';

if ($search !== '') {

    $search = mysqli_real_escape_string($conn, $search);

    $where = "
        WHERE
            categories.category_name LIKE '%$search%'
            OR users.username LIKE '%$search%'
    ";
}

$sql_category = "
SELECT *,
categories.category_name AS category_name,
categories.created_at AS waktu,
users.username AS user_name
FROM categories
INNER JOIN users
ON categories.followed_up_by = users.user_id
$where
ORDER BY categories.created_at ASC
";

$query_category = mysqli_query($conn, $sql_category);

function renderCategoryRows(mysqli_result $query_category): string
{
    $category = 1;

    ob_start();

    while ($result = mysqli_fetch_assoc($query_category)) {

        $id = $result['category_id'];
?>

        <tr>

            <td>
                <span class="table-id">#<?= $category++; ?></span>
            </td>

            <td>
                <div class="table-name">
                    <?= htmlspecialchars($result['category_name']); ?>
                </div>
            </td>

            <td class="table-muted">
                <?= $result['user_name']; ?>
            </td>

            <td class="table-muted">
                <?= $result['followed_up_at'] ?? '-'; ?>
            </td>

            <td class="table-muted">
                <?= $result['waktu']; ?>
            </td>

            <td>
                <div class="table-actions">

                    <a href="edit/edit_category.php?category_id=<?= $id; ?>" class="table-action">
                        <i class="fa-solid fa-pen"></i>
                    </a>

                    <a
                        href="delete/delete_category.php?category_id=<?= $id; ?>"
                        class="table-action table-action-danger btn-delete-category">

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

    if (mysqli_num_rows($query_category) > 0) {
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

                    <?= renderCategoryRows($query_category); ?>

                </tbody>

            </table>

        </div>

    <?php
    } else {
    ?>

        <div class="empty-state">

            <div class="empty-state-icon">
                <i class="fa-solid fa-layer-group"></i>
            </div>

            <h3>No Categories Found</h3>

            <p>
                We couldn't find any categories matching your search.
            </p>

        </div>

<?php
    }

    $tableHtml = ob_get_clean();

    echo json_encode([
        'table' => $tableHtml
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
                <span>Category successfully added.</span>
            </div>
        <?php endif; ?>

        <?php if ($_GET['success'] === 'updated') : ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i>
                <span>Category successfully updated.</span>
            </div>
        <?php endif; ?>

    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'category_has_products') : ?>
        <div class="alert alert-danger">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>
                Category cannot be deleted because it still has products.
            </span>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['success']) && $_GET['success'] === 'deleted') : ?>
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check"></i>
            <span>
                Category Successfully Deleted.
            </span>
        </div>
    <?php endif; ?>

    <div class="page-header">
        <a href="../dashboard.php" class="btn-back">
            <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
        </a>

        <a href="add/add_category.php" class="btn-add">
            Add Category <i class="fa-solid fa-plus"></i>
        </a>
    </div>

    <div class="table-toolbar">
        <form method="GET" class="table-search" id="searchForm">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>

                <input
                    type="text"
                    id="searchInput"
                    autocomplete="off"
                    placeholder="Search category..."
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
                    <?= renderCategoryRows($query_category); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>