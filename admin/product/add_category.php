<?php
include "../security.php";
include "../../config/connection.php";

if (isset($_POST['save'])) {
    $category_id = (int) $_POST['category_id'];
    $name = trim($_POST['name']);


    if ($category_id == '' || $name == '') {
        $error = "All Fields Required.";
    } else { 
        $sql = "insert into categories (category_id, name) values('$category_id','$name')";
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
    <title>Add Category</title>
</head>
<body>

<h1>Add Category</h1>

<a href="../dashboard.php">Back</a>

<br><br>

<?php if (isset($error)) : ?>
    <p style="color:red;"><?= $error; ?></p>
<?php endif; ?>

<form method="POST">
    <label>Category Id</label><br>
    <input type="number" name="category_id">
    <br><br>

    <label>Name</label><br>
    <input type="text" name="name">
    <br><br>

    <button type="submit" name="save">Add</button>
</form>

</body>
</html>