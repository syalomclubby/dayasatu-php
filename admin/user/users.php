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

    <?php if (isset($_GET['error'])) : 
    $error = $_GET['error'];
    $message = "";

    if ($error === 'user_has_products') {
        $message = "products";
    } elseif ($error === 'user_has_brands') {
        $message = "brands";
    } elseif ($error === 'user_has_categories') {
        $message = "categories";
    }
    elseif ($error === 'cannot_delete' && isset($_GET['relations'])) {
        $relations_str = $_GET['relations'];
        $last_comma_pos = strrpos($relations_str, ',');
        if ($last_comma_pos !== false) {
            $message = substr_replace($relations_str, ' and ', $last_comma_pos, 1);
        } else {
            $message = $relations_str;
        }
    }

    if (!empty($message)) :
?>
    <div class="alert alert-danger">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span>
            User cannot be deleted because they still have <?php echo htmlspecialchars($message); ?>.
        </span>
    </div>
<?php 
    endif;
endif; 
?>

    <?php if (isset($_GET['success']) && $_GET['success'] === 'deleted') : ?>
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check"></i>
            <span>
                User successfully deleted.
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


                    $logged_in_id = $_SESSION['user_id'];
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
                                <a href="edit_user.php?user_id=<?= $id; ?>" class="table-action" aria-label="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <?php if ($id != $logged_in_id) : ?>
                                <a
                                    href="delete_user.php?user_id=<?= $id; ?>"
                                    class="table-action table-action-danger btn-delete-user"
                                    aria-label="Delete">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                                    <?php else : ?>
                                        <span class="table-muted" style="font-size: 12px; font-style: italic; color: var(--text-light);">
                                            <i class="fa-solid fa-lock"></i> You
                                        </span>
                                <?php endif; ?>
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
    document.querySelectorAll('.btn-delete-user').forEach((button) => {

        button.addEventListener('click', function(event) {

            event.preventDefault();

            const url = this.getAttribute('href');

            Swal.fire({
                title: 'Delete User?',
                text: 'User yang dihapus tidak dapat dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Delete User',
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