<?php
require_once __DIR__ . "/../config/connection.php";

// Mencegah file diakses langsung
if (!defined('DAYASATU')) {
    exit('No direct script access allowed.');
}

/* 
|============================================================
| BRAND
|============================================================ 
*/

$sql_brands = "
    SELECT
        brand_id,
        brand_name
    FROM brands
    ORDER BY brand_name ASC
";

$query_brands = mysqli_query($conn, $sql_brands);

if (!$query_brands) {
    die("Query Brand Error : " . mysqli_error($conn));
}


/* 
|============================================================
| CATEGORY
|============================================================ 
*/

$sql_categories = "
    SELECT
        category_id,
        category_name
    FROM categories
    ORDER BY category_name ASC
";

$query_categories = mysqli_query($conn, $sql_categories);

if (!$query_categories) {
    die("Query Category Error : " . mysqli_error($conn));
}

/*
|============================================================
| PRODUCTS
|============================================================ 
*/

$sql_products = "
SELECT

    products.product_id,
    products.product_name,
    products.product_description,
    products.product_image,
    products.created_at,

    brands.brand_id,
    brands.brand_name,

    categories.category_id,
    categories.category_name,

    (
        SELECT MIN(product_price)
        FROM product_prices
        WHERE product_prices.product_id = products.product_id
    ) AS min_price,

    (
        SELECT MAX(product_price)
        FROM product_prices
        WHERE product_prices.product_id = products.product_id
    ) AS max_price,

    (
        SELECT GROUP_CONCAT(
            product_price
            ORDER BY product_price ASC
            SEPARATOR ','
        )
        FROM product_prices
        WHERE product_prices.product_id = products.product_id
    ) AS prices

FROM products

INNER JOIN brands
ON products.brand_id = brands.brand_id

INNER JOIN categories
ON products.category_id = categories.category_id

ORDER BY
products.created_at DESC,
products.product_name ASC
";

$query_products = mysqli_query($conn, $sql_products);

if (!$query_products) {
    die("Query Product Error : " . mysqli_error($conn));
}
