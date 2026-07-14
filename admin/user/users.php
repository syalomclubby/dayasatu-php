<?php

require_once __DIR__ . "/../security.php";
require_once __DIR__ . "/../../config/connection.php";

$search = trim($_GET['search'] ?? '');

$where = '';

if ($search !== '') {

    $search = mysqli_real_escape_string($conn, $search);

    $where = "
        WHERE
            username LIKE '%$search%'
            OR role LIKE '%$search%'
    ";
}

$sql_user = "SELECT *,
created_at AS waktu
FROM users
$where
";
$query_user = mysqli_query($conn, $sql_user);

function renderUserRows(mysqli_result $query_user): string
{
    $user = 1;
    $logged_in_id = $_SESSION['user_id'];

    ob_start();

    while ($result = mysqli_fetch_assoc($query_user)) {

        $id = $result['user_id'];
?>

        <tr>

            <td>
                <span class="table-id">#<?= $user++; ?></span>
            </td>

            <td>
                <div class="table-name">
                    <?= htmlspecialchars($result['username']); ?>
                </div>
            </td>

            <td class="table-muted">
                <?= $result['role'] == 1 ? 'Super Admin' : 'Admin'; ?>
            </td>

            <td class="table-muted">
                <?= $result['waktu']; ?>
            </td>

            <td>

                <div class="table-actions">

                    <a
                        href="edit_user.php?user_id=<?= $id; ?>"
                        class="table-action">

                        <i class="fa-solid fa-pen"></i>

                    </a>

                    <?php if ($id != $logged_in_id): ?>

                        <a
                            href="delete_user.php?user_id=<?= $id; ?>"
                            class="table-action table-action-danger btn-delete-user">

                            <i class="fa-solid fa-trash"></i>

                        </a>

                    <?php else: ?>

                        <span
                            class="table-muted"
                            style="font-size:12px;font-style:italic;color:var(--text-light);">

                            <i class="fa-solid fa-lock"></i>
                            You

                        </span>

                    <?php endif; ?>

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

    if (mysqli_num_rows($query_user) > 0) {
    ?>

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

                    <?= renderUserRows($query_user); ?>

                </tbody>

            </table>

        </div>

    <?php
    } else {
    ?>

        <div class="empty-state">

            <div class="empty-state-icon">
                <i class="fa-solid fa-users"></i>
            </div>

            <h3>No Users Found</h3>

            <p>
                We couldn't find any users matching your search.
            </p>

        </div>

<?php
    }

    echo json_encode([
        'table' => ob_get_clean()
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
        } elseif ($error === 'cannot_delete' && isset($_GET['relations'])) {
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

    <div class="table-toolbar">
        <form class="table-search">

            <div class="search-box">

                <i class="fa-solid fa-magnifying-glass"></i>

                <input
                    id="searchInput"
                    type="text"
                    autocomplete="off"
                    placeholder="Search user..."
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
                        <th>Roles</th>
                        <th>Created At</th>
                        <th class="table-actions-head"></th>
                    </tr>
                </thead>
                <tbody>

                    <?= renderUserRows($query_user); ?>

                </tbody>
            </table>
        </div>
    </div>
</div>