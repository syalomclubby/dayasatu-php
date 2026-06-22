<?php

require_once __DIR__ . "/../../security.php";
require_once __DIR__ . "/../../../config/connection.php";

$brand_id = filter_input(INPUT_GET, 'brand_id', FILTER_VALIDATE_INT);

if (!$brand_id) {
    header("Location: ../brands.php");
    exit;
}

$sqlCheck = "
    SELECT COUNT(*) AS total
    FROM products
    WHERE brand_id = ?
";

$stmtCheck = mysqli_prepare($conn, $sqlCheck);

mysqli_stmt_bind_param($stmtCheck, "i", $brand_id);
mysqli_stmt_execute($stmtCheck);

$resultCheck = mysqli_stmt_get_result($stmtCheck);
$rowCheck = mysqli_fetch_assoc($resultCheck);

mysqli_stmt_close($stmtCheck);

if (($rowCheck['total'] ?? 0) > 0) {

    header("Location: ../brands.php?error=brand_has_products");
    exit;
}

$sqlDelete = "DELETE FROM brands WHERE brand_id = ?";
$stmtDelete = mysqli_prepare($conn, $sqlDelete);

if ($stmtDelete) {

    mysqli_stmt_bind_param($stmtDelete, "i", $brand_id);
    mysqli_stmt_execute($stmtDelete);

    mysqli_stmt_close($stmtDelete);
}

header("Location: ../brands.php?success=deleted");
exit;
