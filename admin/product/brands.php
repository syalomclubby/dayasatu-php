<?php
require_once __DIR__ . "/../security.php";
require_once __DIR__ . "/../../config/connection.php";

$sql_brand = "SELECT *,
categories.name AS category_name,
brands.name AS brand_name
FROM brands
INNER JOIN categories ON brands.category_id = categories.category_id
 ";
$query_brand = mysqli_query($conn, $sql_brand);

?>

<?php include '../partials/sidebar.php' ?>

<div class="content">
    <a href="../dashboard.php">Back to Dashboard</a>
    <br>
    <br>
    <div class="table-responsive table-card">
        <table class="table">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Category</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th class="table-actions-head"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $brand = 1;
                while ($result = mysqli_fetch_array($query_brand)) {
                    $name = $result['brand_name'];
                    $description = $result['description'];
                    $id = $result['brand_id'];
                    $id2 = $result['category_name'];
                ?>
                    <tr>
                        <td>
                            <span class="table-id">#<?= $brand++; ?></span>
                        </td>

                        <td class="table-muted">
                            <?= $id2; ?>
                        </td>

                        <td>
                            <div class="table-name">
                                <?= $name; ?>
                            </div>
                        </td>

                        <td>
                            <div class="table-description">
                                <?= $description; ?>
                            </div>
                        </td>

                        <td>
                            <div class="table-actions">
                                <a href="" class="table-action" aria-label="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <a href="delete/delete_brand.php?brand_id=<?= $id; ?>"
                                    class="table-action table-action-danger"
                                    aria-label="Delete"
                                    onclick="return confirm('Hapus brand ini?');">
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