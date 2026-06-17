<?php 

require_once __DIR__ . "/../security.php";
require_once __DIR__ . "/../../config/connection.php";

$sql_category = "SELECT * FROM categories";
$query_category = mysqli_query($conn, $sql_category);

?>

<a href="../dashboard.php">Back to Dashboard</a>

<br><br>
<table border="1"> 
    <thead>
        <tr>
            <th>Category Id</th>
            <th>Nama</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php
            $category = 1;
        while($result = mysqli_fetch_array($query_category)){
            $name = $result['name'];
            $id = $result['category_id'];
        ?>
        <tr>
            <td><?= $id ?></td>
            <td><?= $name ?></td>
            <td>
                <a href="">Edit</a> |
                <a href="delete/delete_category.php?category_id=<?= $id; ?>" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
            </td>
        </tr>
        <?php
            $category++;
        }
        ?>
    </tbody>
</table>