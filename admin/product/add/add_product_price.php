<?php
require_once __DIR__ . "/../../security.php";
require_once __DIR__ . "/../../../config/connection.php";



$current_user_id = (int) ($_SESSION['user_id'] ?? 0);
$current_user_name = '';

if ($current_user_id > 0) {
    $sql_current_user = "SELECT username FROM users WHERE user_id = ? LIMIT 1";
    $stmt_current_user = mysqli_prepare($conn, $sql_current_user);

    if ($stmt_current_user) {
        mysqli_stmt_bind_param($stmt_current_user, "i", $current_user_id);
        mysqli_stmt_execute($stmt_current_user);
        $result_current_user = mysqli_stmt_get_result($stmt_current_user);

        if ($row_current_user = mysqli_fetch_assoc($result_current_user)) {
            $current_user_name = $row_current_user['username'] ?? '';
        }

        mysqli_stmt_close($stmt_current_user);
    }
}

/*
|--------------------------------------------------------------------------
| Validate Product ID
|--------------------------------------------------------------------------
*/

if (!isset($_GET['product_id']) || !is_numeric($_GET['product_id'])) {
    header("Location: ../products.php");
    exit();
}

$product_id = (int) $_GET['product_id'];




/*
|--------------------------------------------------------------------------
| Get Product Information
|--------------------------------------------------------------------------
*/

$sqlProduct = "
SELECT

    p.product_id,
    p.product_name AS product_name,

    b.brand_name AS brand_name,

    c.category_name AS category_name

FROM products p

INNER JOIN brands b
    ON p.brand_id = b.brand_id

INNER JOIN categories c
    ON p.category_id = c.category_id

WHERE p.product_id = ?

LIMIT 1
";

$stmtProduct = mysqli_prepare($conn, $sqlProduct);

mysqli_stmt_bind_param(
    $stmtProduct,
    "i",
    $product_id 
);

mysqli_stmt_execute($stmtProduct);

$productResult = mysqli_stmt_get_result($stmtProduct);

if (mysqli_num_rows($productResult) == 0) {

    header("Location: ../products.php");
    exit();

}

$product = mysqli_fetch_assoc($productResult);

/*
|--------------------------------------------------------------------------
| Initial Variables
|--------------------------------------------------------------------------
*/

$size = "";
$price = "";

$errors = [];


// SUBMIT

if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $size = trim($_POST['product_size']);
        $price = trim($_POST['product_price']);

       // VALIDATION

        if (empty($size)) {
            $errors[] = "Size is required.";
        }

        if (empty($price)) {
            $errors[] = "Price is required.";
        }

        // CONVERT PRICE

        $price = str_replace('.', '', $price);

        if (!is_numeric($price)) {
            $errors[] = "Price must be numeric.";

        } else {
            $price = (float) $price;
            if ($price <= 0) {
                $errors[] = "Price must be greater than zero.";
            }
        }

        // DOUBLE VALIDATION

        if (empty($errors)) {

            $sqlCheck = "
                SELECT price_id
                FROM product_prices
                WHERE product_id = ?
                AND LOWER(TRIM(product_size)) = LOWER(TRIM(?))
                LIMIT 1
            ";

            $stmtCheck = mysqli_prepare($conn, $sqlCheck);

            mysqli_stmt_bind_param(
                $stmtCheck,
                "is",
                $product_id,
                $size
            );

            mysqli_stmt_execute($stmtCheck);
            mysqli_stmt_store_result($stmtCheck);
            if (mysqli_stmt_num_rows($stmtCheck) > 0) {
                $errors[] = "Size already exists for this product.";
            }

            mysqli_stmt_close($stmtCheck);
        }

        // SAVE DATA

        if (empty($errors)) {

            $followed_up_by = $_SESSION['user_id'];

            $sqlInsert = "

                INSERT INTO product_prices
                (
                    product_id,
                    product_size,
                    product_price,
                    followed_up_by,
                    followed_up_at,
                    created_at
                )

                VALUES
                (
                    ?,
                    ?,
                    ?,
                    ?,
                    NOW(),
                    NOW()
                )
            ";

            $stmtInsert = mysqli_prepare($conn, $sqlInsert);
            mysqli_stmt_bind_param(
                $stmtInsert,
                "isdi",
                $product_id,
                $size,
                $price,
                $followed_up_by
            );

        if (mysqli_stmt_execute($stmtInsert)) {
        mysqli_stmt_close($stmtInsert);
        header("Location: ../product_prices.php?product_id=" . $product_id . "&success=added");
        exit();
        } else{
            mysqli_stmt_close($stmtInsert);
            $errors[] = "Failed to save product price.";
        }
    }
}


require_once __DIR__ . "/../../partials/sidebar.php";
?>


<div class="content">
    <div class="main-content">

         <!-- PAGE HEADER -->

        <div class="page-header">
            <div>
                <h1>Add Product Price</h1>
                <p>Add a new size and price for this product.</p>
            </div>

            <div class="action-group">
                <a
                    href="../product_prices.php?product_id=<?= $product_id; ?>"
                    class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                    Back
                </a>
            </div>
        </div>

        <!-- PRODUCT INFORMATION -->

        <div class="card card-info-header">
            <div class="card-header">
                <h2>Product Information</h2>
            </div>

            <div class="card-body">
                <div class="form-group">
                    <label>Product</label>
                    <input
                        type="text"
                        class="form-control"
                        readonly
                        value="<?= htmlspecialchars($product['product_name']); ?>">
                </div>

                <div class="form-group">
                    <label>Brand</label>
                    <input
                        type="text"
                        class="form-control"
                        readonly
                        value="<?= htmlspecialchars($product['brand_name']); ?>">
                </div>

                <div class="form-group">
                    <label>Category</label>
                    <input
                        type="text"
                        class="form-control"
                        readonly
                        value="<?= htmlspecialchars($product['category_name']); ?>">
                </div>
            </div>
        </div>

        <!-- PRICE FORM -->

        <div class="card">
            <div class="card-header">
                <h2>Price Information</h2>
            </div>

            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>
                            Size
                        </label>

                        <input
                            type="text"
                            name="product_size"
                            class="form-control"
                            placeholder="Example : 5 Kg"
                            value="<?= htmlspecialchars($size); ?>"
                            required>
                        <small>
                            Example:
                            1 Kg,
                            5 Kg,
                            20 Kg,
                            500 ml
                        </small>
                    </div>

                    <div class="form-group">
                        <label>
                            Price
                        </label>
                        <input
                            type="text"
                            id="price"
                            name="product_price"
                            class="form-control"
                            placeholder="70.000"
                            autocomplete="off"
                            value="<?= htmlspecialchars($price); ?>"
                            required>
                    </div>

                    <div class="action-group">
                        <button
                            type="submit"
                            class="btn-add"> Add Price
                            <i class="fas fa-save"></i>
                        </button>

                        <a
                            href="../product_prices.php?product_id=<?= $product_id; ?>"
                            class="btn-back"> Cancel &nbsp
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    const priceInput = document.getElementById('price');
    priceInput.addEventListener('input', function () {
        let value = this.value.replace(/\D/g, '');
        if (value === '') {
            this.value = '';
            return;
        }
        this.value = new Intl.NumberFormat('id-ID').format(value);
    });
    </script>
</div>


<?php
mysqli_stmt_close($stmtProduct);
mysqli_close($conn);
?>
