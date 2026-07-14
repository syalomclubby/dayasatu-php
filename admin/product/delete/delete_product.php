<?php

require_once __DIR__ . "/../../security.php";
require_once __DIR__ . "/../../../config/connection.php";

$product_id = filter_input(INPUT_GET, 'product_id', FILTER_VALIDATE_INT);

if (!$product_id) {
    header("Location: ../products.php");
    exit;
}

$sql_product = "
    SELECT
        products.product_image,
        brands.brand_name AS brand_name
    FROM products
    LEFT JOIN brands
        ON products.brand_id = brands.brand_id
    WHERE products.product_id = ?
    LIMIT 1
";

$stmt_product = mysqli_prepare($conn, $sql_product);

if (!$stmt_product) {
    header("Location: ../products.php");
    exit;
}

mysqli_stmt_bind_param($stmt_product, "i", $product_id);
mysqli_stmt_execute($stmt_product);

$result_product = mysqli_stmt_get_result($stmt_product);
$product = mysqli_fetch_assoc($result_product);

mysqli_stmt_close($stmt_product);

if (!$product) {
    header("Location: ../products.php");
    exit;
}

$brand_folder = strtolower(trim($product['brand_name']));

$brand_folder = preg_replace('/[\\\\\/:*?"<>|]/', '', $brand_folder);
$brand_folder = preg_replace('/\s+/', ' ', trim($brand_folder));

$image_path =
    __DIR__
    . "/../../../assets/images/products/"
    . $brand_folder
    . "/"
    . $product['product_image']
    . ".png";

$sql_delete = "DELETE FROM products WHERE product_id = ?";
$stmt_delete = mysqli_prepare($conn, $sql_delete);

if ($stmt_delete) {

    mysqli_stmt_bind_param($stmt_delete, "i", $product_id);

    if (mysqli_stmt_execute($stmt_delete)) {

        if (
            !empty($product['product_image']) &&
            file_exists($image_path)
        ) {
            unlink($image_path);
        }
    }

    mysqli_stmt_close($stmt_delete);
}

header("Location: ../products.php?success=deleted");
exit;
