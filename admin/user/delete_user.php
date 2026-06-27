<?php

require_once __DIR__ . "/../security.php";
require_once __DIR__ . "/../../config/connection.php";

$user_id = filter_input(INPUT_GET, 'user_id', FILTER_VALIDATE_INT);

if (!$user_id) {
    header("Location: users.php");
    exit;
}

$connected_tables = [];

$sqlProducts = "SELECT COUNT(*) FROM products WHERE followed_up_by = ?";
if ($stmt = mysqli_prepare($conn, $sqlProducts)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $count);
    mysqli_stmt_fetch($stmt);
    if ($count > 0) {
        $connected_tables[] = 'products';
    }
    mysqli_stmt_close($stmt);
}

$sqlBrands = "SELECT COUNT(*) FROM brands WHERE followed_up_by = ?";
if ($stmt = mysqli_prepare($conn, $sqlBrands)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $count);
    mysqli_stmt_fetch($stmt);
    if ($count > 0) {
        $connected_tables[] = 'brands';
    }
    mysqli_stmt_close($stmt);
}

$sqlCategories = "SELECT COUNT(*) FROM categories WHERE followed_up_by = ?";
if ($stmt = mysqli_prepare($conn, $sqlCategories)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $count);
    mysqli_stmt_fetch($stmt);
    if ($count > 0) {
        $connected_tables[] = 'categories';
    }
    mysqli_stmt_close($stmt);
}

if (!empty($connected_tables)) {
    $relations = implode(',', $connected_tables);
    header("Location: users.php?error=cannot_delete&relations=" . $relations);
    exit();
}

$sqlDelete = "DELETE FROM users WHERE user_id = ?";
$stmtDelete = mysqli_prepare($conn, $sqlDelete);

if (!$stmtDelete) {
    header("Location: users.php?error=delete_failed");
    exit;
}

mysqli_stmt_bind_param($stmtDelete, "i", $user_id);

if (mysqli_stmt_execute($stmtDelete)) {

    mysqli_stmt_close($stmtDelete);

    header("Location: users.php?success=deleted");
    exit;
}

mysqli_stmt_close($stmtDelete);

header("Location: users.php?error=delete_failed");
exit;
