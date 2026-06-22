<?php

require_once __DIR__ . "/../security.php";
require_once __DIR__ . "/../../config/connection.php";

$sql_category = "SELECT * FROM categories";
$query_category = mysqli_query($conn, $sql_category);

?>

<?php include '../partials/sidebar.php' ?>

<div class="content">

    <a href="../dashboard.php">Back to Dashboard</a>

    <br><br>
    <div class="table-responsive table-card">
        <table class="table">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th class="table-actions-head"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $category = 1;
                while ($result = mysqli_fetch_array($query_category)) {
                    $name = $result['name'];
                    $id = $result['category_id'];
                ?>
                    <tr>
                        <td>
                            <span class="table-id">#<?= $category++; ?></span>
                        </td>

                        <td>
                            <div class="table-name">
                                <?= $name; ?>
                            </div>
                        </td>

                        <td>
                            <div class="table-actions">
                                <a href="" class="table-action" aria-label="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <a href="delete/delete_category.php?category_id=<?= $id; ?>"
                                    class="table-action table-action-danger"
                                    aria-label="Delete"
                                    onclick="return confirm('Yakin ingin menghapus data ini?');">
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