<?php
require_once __DIR__ . "/../security.php";
require_once __DIR__ . "/../../config/connection.php";

$sql_products = "SELECT *, 
products.name AS products_name,
products.followed_up_at AS products_at,
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

    <?php if (isset($_GET['success']) && $_GET['success'] === 'deleted') : ?>
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check"></i>
            <span>
                Product berhasil dihapus.
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

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Brand</th>
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
                            <?php
                            $brand_folder = strtolower(trim($result['brand_name']));
                            ?>

                            <img
                                src="../../assets/images/products/<?= htmlspecialchars($brand_folder); ?>/<?= htmlspecialchars($result['image']); ?>.png"
                                alt="<?= htmlspecialchars($result['products_name']); ?>"
                                class="table-image">
                        </td>

                        <td class="table-muted">
                            <?= $result['user_name']; ?>
                        </td>

                        <td class="table-muted">
                            <?= $result['products_at']; ?>
                        </td>

                        <td class="table-muted">
                            <?= $result['created_at']; ?>
                        </td>

                        <td>
                            <div class="table-actions">
                                <a href="edit/edit_product.php?product_id=<?= $result['product_id']; ?>" class="table-action" aria-label="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <a
                                    href="delete/delete_product.php?product_id=<?= $result['product_id']; ?>"
                                    class="table-action table-action-danger btn-delete-product"
                                    aria-label="Delete">
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

<script>
    document.querySelectorAll('.btn-delete-product').forEach((button) => {

        button.addEventListener('click', function(event) {

            event.preventDefault();

            const url = this.getAttribute('href');

            Swal.fire({
                title: 'Delete Product?',
                text: 'Produk yang dihapus tidak dapat dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Delete Product',
                cancelButtonText: 'Cancel',
                reverseButtons: true,

                customClass: {
                    popup: 'swal-popup',
                    title: 'swal-title',
                    htmlContainer: 'swal-text',
                    confirmButton: 'swal-btn-delete',
                    cancelButton: 'swal-btn-cancel'
                },

                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });

        });

    });
</script>