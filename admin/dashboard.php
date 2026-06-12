<?php 
include "security.php";
include "../config/connection.php";

$sql_category = "SELECT * FROM categories";
$query_category = mysqli_query($conn, $sql_category);


$sql_brand = "SELECT *,
categories.name AS category_name,
brands.name AS brand_name
FROM brands
INNER JOIN categories ON brands.category_id = categories.category_id
 ";
$query_brand = mysqli_query($conn, $sql_brand);


$sql_products = "SELECT *, 
products.name AS products_name,
products.description AS products_desc,
brands.name AS brand_name,
users.name AS user_name
FROM products 
INNER JOIN users ON products.followed_up_by = users.user_id
INNER JOIN brands ON products.brand_id = brands.brand_id
";

$query_product = mysqli_query($conn, $sql_products);

?>
<h1>Admin Dashboard</h1>
<?php
echo "Hello, ".$username . "!";

?> 
<br>
<br>

<h2>Categories Table</h2>
<a href="product/add_category.php">Add Category</a>
<table border="1">
    <thead>
        <tr>
            <th>Category Id</th>
            <th>Name</th>
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
            <td><?= $id; ?></td>
            <td><?= $name; ?></td>
            <td> <a href="">Edit</a> | <a href="product/delete_category.php?category_id=<?= $id; ?>">Hapus</a></td>
    </tr>
    <?php
            $category++;
        }
        ?>
    </tbody>
</table>

<br>
<br>

<h2>Brands Table</h2>
<a href="product/add_brand.php">Add Brand</a>
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
            <td> <a href="">Edit</a> | <a href="product/delete_brand.php?brand_id=<?= $id; ?>">Hapus</a></td>
    </tr>
    <?php
            $brand++;
        }
        ?>
    </tbody>
</table>

<br>
<br>

<h2>Products Table</h2>
<a href="product/add_product.php">Add Product</a> 
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
            $description = $result['products_desc'];
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
            <td><img src="../assets/images/products/<?= $image; ?>" width="100px"></td>
            <td><?= $followedby; ?></td>
            <td><?= $followedat; ?></td>
            <td><?= $created; ?></td>
            <td> <a href="">Edit</a> | <a href="product/delete_product.php?product_id=<?= $id; ?>">Hapus</a></td>
    </tr>
    </tbody>
    <?php
            $product++;
        }
        ?>
</table>
<br>
<a href="logout.php">Logout</a>
