<?php
include "../security.php";
include "../../config/connection.php";

if (isset($_POST['save'])) {
    $brand_id = (int) $_POST['brand_id'];
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $image = trim($_POST['image']);
    $followed_up_by = trim($_POST['followed_up_by']);

    if ($brand_id == '' || $name == '' || $description == '' || $price == '' || $image == '' || $followed_up_by == '') {
        $error = "All Fields Required.";
    } else { 
        $sql = "insert into products (brand_id, name, description, price, image, followed_up_by) 
        values('$brand_id','$name','$description','$price','$image','$followed_up_by')";
        $query = mysqli_query($conn, $sql);

        if ($query) {
            header("Location: ../dashboard.php");
            exit;
        } else {
            $error = "Data failed to save.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Products</title>
</head>
<body>

<h1>Add Products</h1>

<a href="../dashboard.php">Back</a>

<br><br>

<?php if (isset($error)) : ?>
    <p style="color:red;"><?= $error; ?></p>
<?php endif; ?>

<form method="POST">
    <label>Brand Id</label><br>
    <input type="number" name="brand_id">
    <br><br>
    <label>Name</label><br>
    <input type="text" name="name">
    <br><br>
    <label>Description</label><br>
    <input type="text" name="description">
    <br><br>
    <label>Price</label><br>
    <input type="text" name="price">
    <br><br>
    <label>Image</label><br>
    <input type="text" name="image">
    <br><br>
    <label>followed_up_by</label><br>
    <input type="text" name="followed_up_by">
    <br><br>

    <button type="submit" name="save">Add</button>
</form>

</body>
</html>