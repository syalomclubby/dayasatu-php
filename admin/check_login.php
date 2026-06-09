<?php
session_start();
 
include "../config/connection.php";

$username = $_POST['name'];
$password = md5($_POST['password']); 

if (empty($username) || empty($password)){
    echo "Username dan Password wajib diisi.";
    exit;
}
$query = mysqli_query($conn, "SELECT * FROM users WHERE name='$username' AND password='$password'");

$num = mysqli_num_rows($query); 

if ($num > 0){ 
    $data = mysqli_fetch_assoc($query);

    $_SESSION['id'] = $data['id'];
    $_SESSION['name'] = $data['name'];

    header("Location: dashboard.php");
} else {    
    header("Location: login.php")
    ?>
<?php
}
?>