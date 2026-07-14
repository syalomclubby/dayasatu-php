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

function formatRupiah(float|int $price): string
{
    return 'Rp' . number_format($price, 0, ',', '.');
}

function formatDateTime(?string $datetime): string
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
    p.product_name AS product_name,
    b.brand_name AS brand_name,
    c.category_name AS category_name

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
    pp.product_size,
    pp.product_price,
    pp.followed_up_at,
    pp.created_at AS waktu,

    u.username AS admin_name

FROM product_prices pp

LEFT JOIN users u
    ON pp.followed_up_by = u.user_id

WHERE pp.product_id = ?

ORDER BY
    pp.product_price ASC,
    pp.product_size ASC
";

$stmtPrices = mysqli_prepare($conn, $sqlPrices);

mysqli_stmt_bind_param($stmtPrices, "i", $product_id);

mysqli_stmt_execute($stmtPrices);

$priceResult = mysqli_stmt_get_result($stmtPrices);

$totalPrices = mysqli_num_rows($priceResult);

$pageTitle = "Product Pricing";

?>

<div class="content">

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

    <div class="card card-info-header">
    <!-- Success Alert -->
<?php if (isset($_GET['success'])) : ?>

    <?php if ($_GET['success'] === 'added') : ?>
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check"></i>
            <span>Product price successfully added.</span>
        </div>
    <?php endif; ?>

    <?php if ($_GET['success'] === 'updated') : ?>
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check"></i>
            <span>Product price successfully updated.</span>
        </div>
    <?php endif; ?>

    <?php if ($_GET['success'] === 'deleted') : ?>
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check"></i>
            <span>Product price successfully deleted.</span>
        </div>
    <?php endif; ?>

<?php endif; ?>


<!-- Error Alert -->
<?php if (isset($_GET['error'])) : ?>

    <?php if ($_GET['error'] === 'notfound') : ?>
        <div class="alert alert-danger">
            <i class="fa-solid fa-circle-xmark"></i>
            <span>Product price not found.</span>
        </div>
    <?php endif; ?>

    <?php if ($_GET['error'] === 'failed') : ?>
        <div class="alert alert-danger">
            <i class="fa-solid fa-circle-xmark"></i>
            <span>Failed to process product price.</span>
        </div>
    <?php endif; ?>

<?php endif; ?>

        <div class="card-header">
            <h2>Product Information</h2>
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
                        <?= htmlspecialchars($row['product_size']); ?>
                    </td>
                    <td>
                        <?= formatRupiah($row['product_price']); ?>
                    </td>
                    <td>
                        <?= htmlspecialchars($row['admin_name'] ?? '-'); ?>
                    </td>
                    <td>
                        <?= formatDateTime($row['followed_up_at']); ?>
                    </td>
                    <td>
                        <?= formatDateTime($row['waktu']); ?>
                    </td>
                    <td>
                        <div class="table-actions">
                             <a
                            href="edit/edit_product_price.php?price_id=<?= $row['price_id']; ?>"
                            class="table-action table-action-danger" aria-label="Edit">
                            <i class="fas fa-edit"></i>
                            </a>

                            <form
                                action="delete/delete_product_price.php"
                                method="POST"
                                class="delete-form">
                                <input
                                    type="hidden"
                                    name="price_id"
                                    value="<?= $row['price_id']; ?>">
                                <button
                                    type="submit"
                                    class="table-action table-action-danger"
                                        aria-label="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
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
</div>


<?php

mysqli_stmt_close($stmtProduct);
mysqli_stmt_close($stmtPrices);

mysqli_close($conn);

?>

<script>
    document.querySelectorAll(".delete-form").forEach(form => {

    form.addEventListener("submit", function(e){

        e.preventDefault();

            Swal.fire({
                title: 'Delete Price?',
                text: 'Harga yang dihapus tidak dapat dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Delete Price',
                cancelButtonText: 'Cancel',
                reverseButtons: true,

                customClass: {
                    popup: 'swal-popup',
                    title: 'swal-title',
                    htmlContainer: 'swal-text',
                    confirmButton: 'swal-btn-delete',
                    cancelButton: 'swal-btn-cancel'
                },

        }).then((result)=>{

            if(result.isConfirmed){
                form.submit();
            }
        });
    });
});
</script>


                