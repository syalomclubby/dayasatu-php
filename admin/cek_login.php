<?php
session_start();
 
include "../auth/koneksi.php";

$username = $_POST['nama'];
$password = md5($_POST['password']);

if (empty($username) || empty($password)){
    echo "Username dan Password wajib diisi.";
    exit;
}
$query = mysqli_query($conn, "SELECT * FROM users WHERE nama='$username' AND password='$password'");

$num = mysqli_num_rows($query);

if ($num > 0){
    $data = mysqli_fetch_assoc($query);

    $_SESSION['id'] = $data['id'];
    $_SESSION['nama'] = $data['nama'];

    header("Location: dashboard.php");
} else {    
    header("Location: login.php")
    ?>
<?php
}
?>