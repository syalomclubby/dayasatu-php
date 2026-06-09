<?php 
session_start();

$username = $_SESSION['name'];

if ($username == ""){
    header("Location: login.php");
    exit;
} 
?> 