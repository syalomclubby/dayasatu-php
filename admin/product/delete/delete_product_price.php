<?php
require_once __DIR__ . "/../../security.php";
require_once __DIR__ . "/../../../config/connection.php";

// Only Accept POST Request

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../products.php");
        exit();
    }

// Validate Price ID

    if (
        !isset($_POST['price_id']) ||
        !is_numeric($_POST['price_id'])
    ) {
        header("Location: ../products.php");
        exit();
    }
    $price_id = (int) $_POST['price_id'];

// Get Product ID

    $sqlGet = "
    SELECT
        product_id
    FROM product_prices
    WHERE price_id = ?
    LIMIT 1
    ";

    $stmtGet = mysqli_prepare($conn, $sqlGet);
    mysqli_stmt_bind_param(
        $stmtGet,
        "i",
        $price_id
    );
    mysqli_stmt_execute($stmtGet);

    $resultGet = mysqli_stmt_get_result($stmtGet);

    if (mysqli_num_rows($resultGet) == 0) {
        mysqli_stmt_close($stmtGet);
        mysqli_close($conn);
        header("Location: ../products.php");
        exit();
    }

    $data = mysqli_fetch_assoc($resultGet);
    $product_id = $data['product_id'];
    mysqli_stmt_close($stmtGet);

// Delete Product Price

    $sqlDelete = "

    DELETE
    FROM product_prices
    WHERE price_id = ?
    ";

    $stmtDelete = mysqli_prepare($conn, $sqlDelete);

    mysqli_stmt_bind_param(
        $stmtDelete,
        "i",
        $price_id
    );

    if (mysqli_stmt_execute($stmtDelete)) {

        mysqli_stmt_close($stmtDelete);
        mysqli_close($conn);

        header("Location: ../product_prices.php?product_id=".$product_id."&success=deleted");
        exit();

    } else {
        mysqli_stmt_close($stmtDelete);
        mysqli_close($conn);

        header("Location: ../product_prices.php?product_id=".$product_id."&error=failed");
        exit();
    }

?>