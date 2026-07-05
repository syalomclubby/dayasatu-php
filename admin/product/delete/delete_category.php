<?php

require_once __DIR__ . "/../../security.php";
require_once __DIR__ . "/../../../config/connection.php";

$category_id = filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);

if (!$category_id) {
    header("Location: ../categories.php");
    exit;
}

// Cek apakah category masih dipakai product

$sqlCheck = "
    SELECT COUNT(*) AS total
    FROM products
    WHERE category_id = ?
";

$stmtCheck = mysqli_prepare($conn, $sqlCheck);

mysqli_stmt_bind_param($stmtCheck, "i", $category_id);
mysqli_stmt_execute($stmtCheck);

$resultCheck = mysqli_stmt_get_result($stmtCheck);
$rowCheck = mysqli_fetch_assoc($resultCheck);

mysqli_stmt_close($stmtCheck);

if (($rowCheck['total'] ?? 0) > 0) {

    header("Location: ../categories.php?error=category_has_products");
    exit;
}

// Hapus Category

$sqlDelete = "DELETE FROM categories WHERE category_id = ?";
$stmtDelete = mysqli_prepare($conn, $sqlDelete);

if (!$stmtDelete) {
    header("Location: ../categories.php?error=delete_failed");
    exit;
}

mysqli_stmt_bind_param($stmtDelete, "i", $category_id);

if (mysqli_stmt_execute($stmtDelete)) {

    mysqli_stmt_close($stmtDelete);

    header("Location: ../categories.php?success=deleted");
    exit;
}

mysqli_stmt_close($stmtDelete);

header("Location: ../categories.php?error=delete_failed");
exit;
