<?php

require_once __DIR__ . "/../../security.php";
require_once __DIR__ . "/../../../config/connection.php";

function deleteDirectory(string $directory): bool
{
    if (!is_dir($directory)) {
        return true;
    }

    $items = scandir($directory);

    foreach ($items as $item) {

        if ($item === '.' || $item === '..') {
            continue;
        }

        $path = $directory . DIRECTORY_SEPARATOR . $item;

        if (is_dir($path)) {
            deleteDirectory($path);
        } else {
            unlink($path);
        }
    }

    return rmdir($directory);
}

$brand_id = filter_input(INPUT_GET, 'brand_id', FILTER_VALIDATE_INT);

if (!$brand_id) {
    header("Location: ../brands.php");
    exit;
}

$sqlBrand = "SELECT brand_name FROM brands WHERE brand_id = ? LIMIT 1";
$stmtBrand = mysqli_prepare($conn, $sqlBrand);

$brandName = '';

if ($stmtBrand) {

    mysqli_stmt_bind_param($stmtBrand, "i", $brand_id);
    mysqli_stmt_execute($stmtBrand);

    $resultBrand = mysqli_stmt_get_result($stmtBrand);

    if ($rowBrand = mysqli_fetch_assoc($resultBrand)) {
        $brandName = $rowBrand['brand_name'];
    }

    mysqli_stmt_close($stmtBrand);
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

    if (mysqli_stmt_execute($stmtDelete)) {

        $folderName = strtolower($brandName);
        $folderName = preg_replace('/[\\\\\/:*?"<>|]/', '', $folderName);
        $folderName = preg_replace('/\s+/', ' ', trim($folderName));

        $folderPath = __DIR__ . "/../../../assets/images/products/" . $folderName;

        deleteDirectory($folderPath);
    }

    mysqli_stmt_close($stmtDelete);
}

header("Location: ../brands.php?success=deleted");
exit;
