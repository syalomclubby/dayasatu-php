 <?php
include "../security.php";
include "../../config/connection.php";

$id = $_GET['category_id'] ?? '';

if ($id == '') {
    header("Location: ../dashboard.php");
    exit;
} 

$sql = "delete from categories where category_id='$id'";
$query = mysqli_query($conn, $sql);

header("Location: ../dashboard.php");
exit;

?>