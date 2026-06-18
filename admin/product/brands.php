<?php 
include "../security.php";
include "../../config/connection.php";

$sql_brand = "SELECT *,
categories.name AS category_name,
brands.name AS brand_name
FROM brands
INNER JOIN categories ON brands.category_id = categories.category_id
 ";
$query_brand = mysqli_query($conn, $sql_brand);



?>
<br>
<a href="../dashboard.php">Back to Dashboard</a>
<br>
<br>
<table border="1">
    <thead>
        <tr>
            <th>Brand Id</th>
            <th>Category Id</th>
            <th>Name</th>
            <th>Description</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
      <?php
            $brand = 1;
        while($result = mysqli_fetch_array($query_brand)){
            $name = $result['brand_name'];
            $description = $result['description'];
            $id = $result['brand_id'];
            $id2 = $result['category_name'];
        ?>
        <tr>
            <td><?= $id; ?></td>
            <td><?= $id2; ?></td>
            <td><?= $name; ?></td>
            <td><?= $description; ?></td>
            <td> <a href="">Edit</a> | <a href="delete/delete_brand.php?brand_id=<?= $id; ?>">Hapus</a></td>
    </tr>
    <?php
            $brand++;
        }
        ?>
    </tbody>
</table>