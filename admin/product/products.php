<?php
require_once __DIR__ . "/../security.php";
require_once __DIR__ . "/../../config/connection.php";

$sql_products = "SELECT *, 
products.name AS products_name,
brands.name AS brand_name,
users.name AS user_name
FROM products 
INNER JOIN users ON products.followed_up_by = users.user_id
INNER JOIN brands ON products.brand_id = brands.brand_id
";

$query_product = mysqli_query($conn, $sql_products);
?>

<?php include '../partials/sidebar.php' ?>

<div class="content">

    <div class="page-header">
        <a href="../dashboard.php" class="btn-back">
            <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
        </a>

        <a href="add_products.php" class="btn-add">
            Add Product <i class="fa-solid fa-plus"></i>
        </a>
    </div>

    <div class="table-responsive table-card">
        <table class="table">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Brand Id</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Image</th>
                    <th>Followed Up By</th>
                    <th>Followed Up At</th>
                    <th>Created At</th>
                    <th class="table-actions-head"></th>
                </tr>
            </thead>

            <tbody>
                <?php
                $i = 1;
                while ($result = mysqli_fetch_array($query_product)) {
                ?>
                    <tr>
                        <td>
                            <span class="table-id">#<?= $i++; ?></span>
                        </td>

                        <td class="table-muted">
                            <?= $result['brand_name']; ?>
                        </td>

                        <td>
                            <div class="table-name">
                                <?= $result['products_name']; ?>
                            </div>
                        </td>

                        <td>
                            <div class="table-description">
                                <?= $result['description']; ?>
                            </div>
                        </td>

                        <td>
                            <span class="table-price">
                                <?= $result['price']; ?>
                            </span>
                        </td>

                        <td>
                            <img
                                src="../../assets/images/products/<?= $result['image']; ?>.png"
                                alt="<?= $result['products_name']; ?>"
                                class="table-image">
                        </td>

                        <td class="table-muted">
                            <?= $result['user_name']; ?>
                        </td>

                        <td class="table-muted">
                            <?= $result['followed_up_at']; ?>
                        </td>

                        <td class="table-muted">
                            <?= $result['created_at']; ?>
                        </td>

                        <td>
                            <div class="table-actions">
                                <a href="" class="table-action" aria-label="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <a
                                    href="delete/delete_products.php?product_id=<?= $result['product_id']; ?>"
                                    class="table-action table-action-danger"
                                    aria-label="Delete"
                                    onclick="return confirm('Hapus produk ini?');">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>