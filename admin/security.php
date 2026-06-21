<?php
session_start();

require_once __DIR__ . '/../config/connection.php';

if (
    !isset($_SESSION['user_id']) ||
    empty($_SESSION['user_id'])
) {
    header("Location: {$base_url}admin/login.php");
    exit;
}
