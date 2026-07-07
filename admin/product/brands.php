<?php
require_once __DIR__ . "/../security.php";
require_once __DIR__ . "/../../config/connection.php";

$sql_brand = "SELECT *,
brands.name AS brand_name,
brands.followed_up_at AS brands_at,
brands.created_at AS waktu,
users.name AS user_name
FROM brands
INNER JOIN users ON brands.followed_up_by = users.user_id
 ";
$query_brand = mysqli_query($conn, $sql_brand);

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
    <div class="table-responsive table-card">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Followed Up By</th>
                    <th>Followed Up At</th>
                    <th>Created At</th>
                    <th class="table-actions-head"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $brand = 1;
                while ($result = mysqli_fetch_array($query_brand)) {
                    $brand_name = $result['brand_name'];
                    $description = $result['description'];
                    $brand_id = $result['brand_id'];
                ?>
                    <tr>
                        <td>
                            <span class="table-id">#<?= $brand++; ?></span>
                        </td>

                        <td>
                            <div class="table-name">
                                <?= htmlspecialchars($brand_name); ?>
                            </div>
                        </td>

                        <td>
                            <div class="table-description">
                                <?= htmlspecialchars($description); ?>
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
                                <a href="edit/edit_brand.php?brand_id=<?= $brand_id; ?>" class="table-action" aria-label="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <a
                                    href="delete/delete_brand.php?brand_id=<?= $brand_id; ?>"
                                    class="table-action table-action-danger btn-delete-brand"
                                    aria-label="Delete">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    document.querySelectorAll('.btn-delete-brand').forEach((button) => {

        button.addEventListener('click', function(event) {

            event.preventDefault();

            const url = this.getAttribute('href');

            Swal.fire({
                title: 'Delete Brand?',
                text: 'Brand yang dihapus tidak dapat dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Delete Brand',
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