<?php 
session_start();

$username = $_SESSION['nama'];

if ($username == ""){
    header("Location: login.php");
    exit;
} 
?> 