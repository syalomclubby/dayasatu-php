<?php
include "../security.php";
include "../../config/connection.php";

if (isset($_POST['save'])) {
    $category_id = (int) $_POST['category_id'];
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);


    if ($category_id == '' || $name == '' || $description == "") {
        $error = "All Fields Required.";
    } else { 
        $sql = "insert into brands (category_id, name, description) values('$category_id','$name', '$description')";
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
    <title>Add Brand</title>
</head>
<body>

<h1>Add Brand</h1>

<a href="../dashboard.php">Back</a>

<br><br>

<?php if (isset($error)) : ?>
    <p style="color:red;"><?= $error; ?></p>
<?php endif; ?>

<form method="POST">
    <label>Category Id</label><br>
    <input type="number" name="category_id">
    <br><br>

    <label>Brand Name</label><br>
    <input type="text" name="name">
    <br><br>

    <label>Description</label><br>
    <textarea name="description" rows="5" cols="40"></textarea>
    <br><br>

    <button type="submit" name="save">Add</button>
</form>

</body>
</html>