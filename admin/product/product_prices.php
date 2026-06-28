<?php

require_once __DIR__ . "/../security.php";
require_once __DIR__ . "/../../config/connection.php";

/*
|--------------------------------------------------------------------------
| Validate Product ID
|--------------------------------------------------------------------------
*/

if (!isset($_GET['product_id']) || !is_numeric($_GET['product_id'])) {
    header("Location: products.php");
    exit();
}

$product_id = (int) $_GET['product_id'];

/*
|--------------------------------------------------------------------------
| Helper Functions
|--------------------------------------------------------------------------
*/

function formatRupiah($price)
{
    return 'Rp' . number_format($price, 0, ',', '.');
}

function formatDateTime($datetime)
{
    if (empty($datetime)) {
        return '-';
    }

    return date('d M Y H:i', strtotime($datetime));
}

/*
|--------------------------------------------------------------------------
| Get Product Information
|--------------------------------------------------------------------------
*/

$sqlProduct = "
SELECT
    p.product_id,
    p.name AS product_name,
    b.name AS brand_name,
    c.name AS category_name

FROM products p

INNER JOIN brands b
    ON p.brand_id = b.brand_id

INNER JOIN categories c
    ON p.category_id = c.category_id

WHERE p.product_id = ?
LIMIT 1
";

$stmtProduct = mysqli_prepare($conn, $sqlProduct);

mysqli_stmt_bind_param($stmtProduct, "i", $product_id);

mysqli_stmt_execute($stmtProduct);

$productResult = mysqli_stmt_get_result($stmtProduct);

if (mysqli_num_rows($productResult) == 0) {
    header("Location: products.php");
    exit();
}

$product = mysqli_fetch_assoc($productResult);

/*
|--------------------------------------------------------------------------
| Get Product Prices
|--------------------------------------------------------------------------
*/

$sqlPrices = "
SELECT

    pp.price_id,
    pp.size,
    pp.price,
    pp.followed_up_at,
    pp.created_at,

    u.name AS admin_name

FROM product_prices pp

LEFT JOIN users u
    ON pp.followed_up_by = u.user_id

WHERE pp.product_id = ?

ORDER BY
    pp.price ASC,
    pp.size ASC
";

$stmtPrices = mysqli_prepare($conn, $sqlPrices);

mysqli_stmt_bind_param($stmtPrices, "i", $product_id);

mysqli_stmt_execute($stmtPrices);

$priceResult = mysqli_stmt_get_result($stmtPrices);

$totalPrices = mysqli_num_rows($priceResult);

$pageTitle = "Product Pricing";

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($pageTitle); ?></title>

    <link
        rel="stylesheet"
        href="../../assets/css/admin.css">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>

<body>

<?php include '../partials/sidebar.php' ?>

<div class="main-content">

    <div class="page-header">

        <div>

            <h1>Pricing Management</h1>

            <p>Manage prices for each product.</p>

        </div>

        <div class="action-group">

            <a
                href="products.php"
                class="btn-back">

                <i class="fas fa-arrow-left"></i>

                Back to Products

            </a>

        </div>

    </div>

    <!-- PRODUCT INFORMATION -->

    <div class="card">

        <div class="card-header">

            <h2>Product Information</h2>

            <a href="products.php" class="btn-back">
                <i class="fas fa-arrow-left"></i>
                Back to Products
            </a>
        </div>

        <div class="card-body">

            <table class="info-table">

                <tr>
                    <th width="180">Product</th>
                    <td>
                        <?= htmlspecialchars($product['product_name']); ?>
                    </td>
                </tr>

                <tr>
                    <th>Brand</th>
                    <td>
                        <?= htmlspecialchars($product['brand_name']); ?>
                    </td>
                </tr>

                <tr>
                    <th>Category</th>
                    <td>
                        <?= htmlspecialchars($product['category_name']); ?>
                    </td>
                </tr>
                <tr>
                    <th>Total Variant</th>
                    <td>
                        <?= $totalPrices; ?> Price(s)
                    </td>
                </tr>
            </table>
        </div>
    </div>



    <!-- PRICE TABLE -->

    <div class="card">
        <div class="card-header">
            <h2>Price List</h2>
            <!-- ACTION -->
            <div class="action-group" style="margin:20px 0;">
                <a
                    href="add/add_product_price.php?product_id=<?= $product_id; ?>"
                    class="btn-add"> Add Price
                    <i class="fas fa-plus"></i> 
                </a>
            </div>
        </div>

        <div class="card-body">

            <?php if ($totalPrices > 0): ?>

            <table class="table">
                <thead>
                    <tr>
                        <th width="60">No</th>
                        <th>Size</th>
                        <th>Price</th>
                        <th>Followed Up By</th>
                        <th>Followed Up At</th>
                        <th>Created At</th>
                        <th width="150">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $no = 1;
                while ($row = mysqli_fetch_assoc($priceResult)):
                ?>

                <tr>
                    <td>
                        <?= $no++; ?>
                    </td>
                    <td>
                        <?= htmlspecialchars($row['size']); ?>
                    </td>
                    <td>
                        <?= formatRupiah($row['price']); ?>
                    </td>
                    <td>
                        <?= htmlspecialchars($row['admin_name'] ?? '-'); ?>
                    </td>
                    <td>
                        <?= formatDateTime($row['followed_up_at']); ?>
                    </td>
                    <td>
                        <?= formatDateTime($row['created_at']); ?>
                    </td>
                    <td>
                         <a
                            href="edit/edit_product_price.php?price_id=<?= $row['price_id']; ?>"
                            class="btn-edit"
                            title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>

                        <a
                            href="delete/delete_product_price.php?price_id=<?= $row['price_id']; ?>"
                            class="btn-delete"
                            onclick="return confirm('Delete this price?');"
                            title="Delete">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>

                <?php endwhile; ?>

                </tbody>

            </table>

            <?php else: ?>

                <div class="empty-state">

                    <i
                        class="fas fa-tags"
                        style="font-size:48px;color:#999;margin-bottom:15px;">
                    </i>

                    <h3>No Product Prices Found</h3>

                    <p>

                        Click <strong>Add Price</strong>
                        to create the first price.

                    </p>

                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

</body>

</html>

<?php

mysqli_stmt_close($stmtProduct);
mysqli_stmt_close($stmtPrices);

mysqli_close($conn);

?>


                