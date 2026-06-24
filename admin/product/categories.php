<?php

require_once __DIR__ . "/../security.php";
require_once __DIR__ . "/../../config/connection.php";

$sql_category = "SELECT *,
categories.name AS category_name,
users.name AS user_name
FROM categories
INNER JOIN users ON categories.followed_up_by = users.user_id
";
$query_category = mysqli_query($conn, $sql_category);

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

    <?php if (isset($_GET['error']) && $_GET['error'] === 'category_has_brands') : ?>
        <div class="alert alert-danger">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>
                Category tidak dapat dihapus karena masih memiliki brand.
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
                <?php
                $category = 1;
                while ($result = mysqli_fetch_array($query_category)) {
                    $name = $result['category_name'];
                    $id = $result['category_id'];
                ?>
                    <tr>
                        <td>
                            <span class="table-id">#<?= $category++; ?></span>
                        </td>

                        <td>
                            <div class="table-name">
                                <?= htmlspecialchars($name); ?>
                            </div>
                        </td>

                        <td class="table-muted">
                            <?= $result['user_name']; ?>
                        </td>

                        <td class="table-muted">
                            <?= $result['followed_up_at'] ?? '-'; ?>
                        </td>

                        <td class="table-muted">
                            <?= $result['created_at']; ?>
                        </td>

                        <td>
                            <div class="table-actions">
                                <a href="edit/edit_category.php?category_id=<?= $id; ?>" class="table-action" aria-label="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <a
                                    href="delete/delete_category.php?category_id=<?= $id; ?>"
                                    class="table-action table-action-danger btn-delete-category"
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
    document.querySelectorAll('.btn-delete-category').forEach((button) => {

        button.addEventListener('click', function(event) {

            event.preventDefault();

            const url = this.getAttribute('href');

            Swal.fire({
                title: 'Delete Category?',
                text: 'Category yang dihapus tidak dapat dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Delete Category',
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