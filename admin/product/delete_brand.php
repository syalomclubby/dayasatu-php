 <?php
include "../security.php";
include "../../config/connection.php";

$id = $_GET['brand_id'] ?? '';

if ($id == '') {
    header("Location: ../dashboard.php");
    exit;
} 

$sql = "delete from brands where brand_id='$id'";
$query = mysqli_query($conn, $sql);

header("Location: ../dashboard.php");
exit;

?>