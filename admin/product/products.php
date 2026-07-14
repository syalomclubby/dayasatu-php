<?php
require_once __DIR__ . "/../security.php";
require_once __DIR__ . "/../../config/connection.php";

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$offset = ($page - 1) * $limit;

$search = trim($_GET['search'] ?? '');

$where = '';

if ($search !== '') {

    $search = mysqli_real_escape_string($conn, $search);

    $where = "
    WHERE
        products.product_name LIKE '%$search%'
        OR brands.brand_name LIKE '%$search%'
        OR categories.category_name LIKE '%$search%'
        OR users.username LIKE '%$search%'
    ";
}

// Hitung total data
$total_query = mysqli_query($conn, "
SELECT COUNT(*) AS total
FROM products
INNER JOIN brands
ON products.brand_id = brands.brand_id
INNER JOIN users
ON products.followed_up_by = users.user_id
INNER JOIN categories
ON products.category_id = categories.category_id
$where
");
$total_data = mysqli_fetch_assoc($total_query)['total'];
$total_pages = ceil($total_data / $limit);

$sql_products = "SELECT *, 
products.product_name AS products_name,
products.followed_up_at AS products_at,
products.created_at AS waktu,
brands.brand_name AS brand_name,
users.username AS user_name,
categories.category_name AS category_name,
(SELECT MIN(product_price) FROM product_prices WHERE product_id = products.product_id) AS min_price,
(SELECT MAX(product_price) FROM product_prices WHERE product_id = products.product_id) AS max_price,
products.product_description
FROM products 
INNER JOIN users ON products.followed_up_by = users.user_id
INNER JOIN brands ON products.brand_id = brands.brand_id
INNER JOIN categories ON products.category_id = categories.category_id
$where
LIMIT $limit OFFSET $offset
";

$query_product = mysqli_query($conn, $sql_products);

function renderProductRows(mysqli_result $query_product, int $offset): string
{
    $i = $offset + 1;

    ob_start();

    while ($result = mysqli_fetch_assoc($query_product)) {
        $brand_folder = strtolower(trim($result['brand_name']));
?>
        <tr>
            <td>
                <span class="table-id">#<?= $i++; ?></span>
            </td>

            <td class="table-muted">
                <?= $result['brand_name']; ?>
            </td>

            <td class="table-muted">
                <?= $result['category_name']; ?>
            </td>

            <td>
                <div class="table-name">
                    <?= $result['products_name']; ?>
                </div>
            </td>

            <td>
                <?php
                if ($result['min_price'] !== null && $result['max_price'] !== null) {

                    if ($result['min_price'] == $result['max_price']) {

                        echo "Rp " . number_format($result['min_price'], 0, ',', '.');
                    } else {

                        echo "Rp " . number_format($result['min_price'], 0, ',', '.') .
                            " - Rp " .
                            number_format($result['max_price'], 0, ',', '.');
                    }
                } else {

                    echo "<em>Belum ada harga</em>";
                }
                ?>
            </td>

            <td>
                <div class="table-description">
                    <?= $result['product_description']; ?>
                </div>
            </td>

            <td>
                <img
                    src="../../assets/images/products/<?= htmlspecialchars($brand_folder) ?>/<?= htmlspecialchars($result['product_image']) ?>.png"
                    class="table-image">
            </td>

            <td class="table-muted">
                <?= $result['user_name']; ?>
            </td>

            <td class="table-muted">
                <?= $result['products_at'] ?? '-'; ?>
            </td>

            <td class="table-muted">
                <?= $result['waktu']; ?>
            </td>

            <td>
                <div class="table-actions">

                    <a href="edit/edit_product.php?product_id=<?= $result['product_id']; ?>" class="table-action">
                        <i class="fa-solid fa-pen"></i>
                    </a>

                    <a href="product_prices.php?product_id=<?= $result['product_id']; ?>" class="table-action">
                        <i class="fa-solid fa-tag"></i>
                    </a>

                    <a
                        href="delete/delete_product.php?product_id=<?= $result['product_id']; ?>"
                        class="table-action table-action-danger btn-delete-product">

                        <i class="fa-solid fa-trash"></i>

                    </a>

                </div>
            </td>

        </tr>

    <?php


    }

    return ob_get_clean();
}
function renderPagination(int $page, int $total_pages): string
{
    ob_start();
    ?>

    <div class="pagination">

        <?php if ($page > 1): ?>
            <a href="#" class="page-link" data-page="<?= $page - 1 ?>">
                <i class="fa-solid fa-angle-left"></i>
            </a>
        <?php endif; ?>

        <?php

        $range = 1;
        $last_page = null;

        for ($p = 1; $p <= $total_pages; $p++) {

            if (
                $p == 1 ||
                $p == $total_pages ||
                ($p >= $page - $range && $p <= $page + $range)
            ) {

                if (isset($last_page) && $last_page + 1 < $p) {
                    echo '<span class="pagination-dots">...</span>';
                }

        ?>

                <a
                    href="#"
                    class="page-link <?= $p == $page ? 'active' : '' ?>"
                    data-page="<?= $p ?>">

                    <?= $p ?>

                </a>

        <?php

                $last_page = $p;
            }
        }

        ?>

        <?php if ($page < $total_pages): ?>
            <a href="#" class="page-link" data-page="<?= $page + 1 ?>">
                <i class="fa-solid fa-angle-right"></i>
            </a>
        <?php endif; ?>

    </div>

    <?php

    return ob_get_clean();
}

// AJAX REQUEST
if (isset($_GET['ajax'])) {

    header('Content-Type: application/json');

    ob_start();

    if (mysqli_num_rows($query_product) > 0) {
    ?>

        <div class="table-responsive">
            <table class="table">

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Brand</th>
                        <th>Cateory</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Description</th>
                        <th>Image</th>
                        <th>Followed Up By</th>
                        <th>Followed Up At</th>
                        <th>Created At</th>
                        <th class="table-actions-head"></th>
                    </tr>
                </thead>

                <tbody id="productTableBody">
                    <?= renderProductRows($query_product, $offset); ?>
                </tbody>

            </table>
        </div>

    <?php
    } else {
    ?>

        <div class="empty-state">

            <div class="empty-state-icon">
                <i class="fa-solid fa-box-open"></i>
            </div>

            <h3>No Products Found</h3>

            <p>
                We couldn't find any products matching your search.
            </p>

        </div>

<?php
    }

    $tableHtml = ob_get_clean();

    echo json_encode([
        'table' => $tableHtml,
        'pagination' => renderPagination($page, $total_pages)
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
                <span>Product successfully added.</span>
            </div>
        <?php endif; ?>

        <?php if ($_GET['success'] === 'updated') : ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i>
                <span>Product successfully updated.</span>
            </div>
        <?php endif; ?>

    <?php endif; ?>

    <?php if (isset($_GET['success']) && $_GET['success'] === 'deleted') : ?>
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check"></i>
            <span>
                Product successfully deleted.
            </span>
        </div>
    <?php endif; ?>

    <div class="page-header">
        <a href="../dashboard.php" class="btn-back">
            <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
        </a>

        <a href="add/add_product.php" class="btn-add">
            Add Product <i class="fa-solid fa-plus"></i>
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
                    placeholder="Search product, brand, category..."
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
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Brand</th>
                        <th>Cateory</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Description</th>
                        <th>Image</th>
                        <th>Followed Up By</th>
                        <th>Followed Up At</th>
                        <th>Created At</th>
                        <th class="table-actions-head"></th>
                    </tr>
                </thead>

                <tbody id="productTableBody">

                    <?= renderProductRows($query_product, $offset); ?>

                </tbody>
            </table>
        </div>
    </div>

    <div class="pagination-wrapper">

        <div id="paginationContainer">

            <?= renderPagination($page, $total_pages); ?>

        </div>

    </div>


</div>