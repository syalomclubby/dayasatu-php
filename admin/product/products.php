<?php
include "../security.php";
include "../../config/connection.php";

$sql_products = "SELECT *, 
products.name AS products_name,
brands.name AS brand_name,
users.name AS user_name
FROM products 
INNER JOIN users ON products.followed_up_by = users.user_id
INNER JOIN brands ON products.brand_id = brands.brand_id
";

$query_product = mysqli_query($conn, $sql_products);
?>
<br>
<a href="../dashboard.php">Back to Dashboard</a>
<br>
<br>
<table border='1'>
    <thead>
        <tr>
            <th>Id</th>
            <th>Brand Id</th>
            <th>Name</th>
            <th>Description</th>
            <th>Price</th>
            <th>Image</th>
            <th>Followed Up By</th>
            <th>Followed Up At</th>
            <th>Created At</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
          <?php
            $product = 1;
        while($result = mysqli_fetch_array($query_product)){
            $name = $result['products_name'];
            $description = $result['description'];
            $price = $result['price'];
            $image = $result['image'];
            $followedby = $result['user_name'];
            $followedat = $result['followed_up_at'];
            $created = $result['created_at'];
            $id = $result['product_id'];
            $id2 = $result['brand_name'];
        ?>
        <tr>
            <td><?= $id; ?></td>
            <td><?= $id2; ?></td>
            <td><?= $name; ?></td>
            <td><?= $description; ?></td>
            <td><?= $price; ?></td>
            <td><img src="../../assets/images/products/<?= $image; ?>.png" width="100px"></td>
            <td><?= $followedby; ?></td>
            <td><?= $followedat; ?></td>
            <td><?= $created; ?></td>
            <td> <a href="">Edit</a> | <a href="delete/delete_products.php?product_id=<?= $id; ?>">Hapus</a></td>
    </tr>
    </tbody>
    <?php
            $product++;
        }
        ?>
</table>