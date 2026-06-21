<?php
session_start();
require_once __DIR__ . '/../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

$name = trim($_POST['name'] ?? '');
$password = $_POST['password'] ?? '';

if ($name === '' || $password === '') {
    $_SESSION['login_error'] = 'Username dan password wajib diisi.';
    header("Location: login.php");
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT user_id, name, password FROM users WHERE name = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $name);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if ($user && ($user['password'] === md5($password) || password_verify($password, $user['password']))) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['name'] = $user['name'];
    unset($_SESSION['login_error']);
    header("Location: dashboard.php");
    exit;
}

$_SESSION['login_error'] = 'Username atau password salah.';
header("Location: login.php");
exit;
