<?php

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
        name
    FROM brands
    ORDER BY name ASC
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
        name
    FROM categories
    ORDER BY name ASC
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
    products.name,
    products.description,
    products.image,
    products.created_at,

    brands.brand_id,
    brands.name AS brand_name,

    categories.category_id,
    categories.name AS category_name,

    (
        SELECT MIN(price)
        FROM product_prices
        WHERE product_prices.product_id = products.product_id
    ) AS min_price,

    (
        SELECT MAX(price)
        FROM product_prices
        WHERE product_prices.product_id = products.product_id
    ) AS max_price

FROM products

INNER JOIN brands
ON products.brand_id = brands.brand_id

INNER JOIN categories
ON products.category_id = categories.category_id

ORDER BY
products.created_at DESC,
products.name ASC
";

$query_products = mysqli_query($conn, $sql_products);

if (!$query_products) {
    die("Query Product Error : " . mysqli_error($conn));
}