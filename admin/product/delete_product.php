 <?php
include "../security.php";
include "../../config/connection.php";

$id = $_GET['product_id'] ?? '';

if ($id == '') {
    header("Location: ../dashboard.php");
    exit;
} 

$sql = "delete from products where product_id='$id'";
$query = mysqli_query($conn, $sql);

header("Location: ../dashboard.php");
exit;

?>