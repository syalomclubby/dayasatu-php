<?php

require_once __DIR__ . "/../security.php";
require_once __DIR__ . "/../../config/connection.php";

$sql_user = "SELECT * FROM users";
$query_user = mysqli_query($conn, $sql_user);

?>

<?php include '../partials/sidebar.php' ?>

<div class="content">

    <?php if (isset($_GET['success'])) : ?>

        <?php if ($_GET['success'] === 'added') : ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i>
                <span>User successfully added.</span>
            </div>
        <?php endif; ?>

        <?php if ($_GET['success'] === 'updated') : ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i>
                <span>User successfully updated.</span>
            </div>
        <?php endif; ?>

    <?php endif; ?>

    <?php if (isset($_GET['success']) && $_GET['success'] === 'deleted') : ?>
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check"></i>
            <span>
                User berhasil dihapus.
            </span>
        </div>
    <?php endif; ?>

    <div class="page-header">
        <a href="../dashboard.php" class="btn-back">
            <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
        </a>

        <a href="add_user.php" class="btn-add">
            Add User <i class="fa-solid fa-plus"></i>
        </a>
    </div>

    <div class="table-responsive table-card">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Roles</th>
                    <th>Created At</th>
                    <th class="table-actions-head"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $user = 1;
                while ($result = mysqli_fetch_array($query_user)) {
                    $name = $result['name'];
                    $id = $result['user_id'];
                    $role = $result['role'];
                ?>
                    <tr>
                        <td>
                            <span class="table-id">#<?= $user++; ?></span>
                        </td>

                        <td>
                            <div class="table-name">
                                <?= htmlspecialchars($name); ?>
                            </div>
                        </td>

                        <td class="table-muted">
                            <?= $result['role'] == 1 ? 'Super Admin' : 'Admin'; ?>
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