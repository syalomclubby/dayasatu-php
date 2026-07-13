<?php

require_once __DIR__ . "/../../security.php";
require_once __DIR__ . "/../../../config/connection.php";

/*
|--------------------------------------------------------------------------
| Validate Price ID
|--------------------------------------------------------------------------
*/

if (!isset($_GET['price_id']) || !is_numeric($_GET['price_id'])) {

    header("Location: ../products.php");
    exit();

}

$price_id = (int) $_GET['price_id'];

/*
|--------------------------------------------------------------------------
| Get Product Price Information
|--------------------------------------------------------------------------
*/

$sqlPrice = "

SELECT

    pp.price_id,
    pp.product_id,
    pp.size,
    pp.price,

    p.name AS product_name,

    b.name AS brand_name,

    c.name AS category_name

FROM product_prices pp

INNER JOIN products p
    ON pp.product_id = p.product_id

INNER JOIN brands b
    ON p.brand_id = b.brand_id

INNER JOIN categories c
    ON p.category_id = c.category_id

WHERE pp.price_id = ?

LIMIT 1

";

$stmtPrice = mysqli_prepare($conn, $sqlPrice);

mysqli_stmt_bind_param(
    $stmtPrice,
    "i",
    $price_id
);

mysqli_stmt_execute($stmtPrice);

$resultPrice = mysqli_stmt_get_result($stmtPrice);

if (mysqli_num_rows($resultPrice) == 0) {

    header("Location: ../products.php");
    exit();

}

$data = mysqli_fetch_assoc($resultPrice);

/*
|--------------------------------------------------------------------------
| Initial Variables
|--------------------------------------------------------------------------
*/

$product_id = $data['product_id'];

$size = $data['size'];

$price_display = number_format(
    $data['price'],
    0,
    ',',
    '.'
);

$errors = [];

/*
|--------------------------------------------------------------------------
| Update Product Price
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $size = trim($_POST['size']);

    $price_display = trim($_POST['price']);

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    if (empty($size)) {
        $errors[] = "Size is required.";
    }

    if (empty($price_display)) {
        $errors[] = "Price is required.";
    }

    /*
    |--------------------------------------------------------------------------
    | Convert Price
    |--------------------------------------------------------------------------
    */

    $price_value = str_replace('.', '', $price_display);

    if (!is_numeric($price_value)) {

        $errors[] = "Price must be numeric.";

    } else {

        $price_value = (float) $price_value;

        if ($price_value <= 0) {
            $errors[] = "Price must be greater than zero.";
        }

    }

    /*
    |--------------------------------------------------------------------------
    | Duplicate Size Validation
    |--------------------------------------------------------------------------
    */

    if (empty($errors)) {

        $sqlCheck = "

            SELECT price_id

            FROM product_prices

            WHERE product_id = ?

            AND LOWER(TRIM(size)) = LOWER(TRIM(?))

            AND price_id <> ?

            LIMIT 1

        ";

        $stmtCheck = mysqli_prepare($conn, $sqlCheck);

        mysqli_stmt_bind_param(

            $stmtCheck,

            "isi",

            $product_id,
            $size,
            $price_id

        );

        mysqli_stmt_execute($stmtCheck);

        mysqli_stmt_store_result($stmtCheck);

        if (mysqli_stmt_num_rows($stmtCheck) > 0) {

            $errors[] = "Size already exists for this product.";

        }

        mysqli_stmt_close($stmtCheck);

    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    if (empty($errors)) {

        $followed_up_by = $_SESSION['user_id'];

        $sqlUpdate = "

            UPDATE product_prices

            SET

                size = ?,

                price = ?,

                followed_up_by = ?,

                followed_up_at = NOW()

            WHERE price_id = ?

        ";

        $stmtUpdate = mysqli_prepare($conn, $sqlUpdate);

        mysqli_stmt_bind_param(

            $stmtUpdate,

            "sdii",

            $size,
            $price_value,
            $followed_up_by,
            $price_id

        );

        if (mysqli_stmt_execute($stmtUpdate)) {

            $_SESSION['success'] = "Price updated successfully.";

            header("Location: ../product_prices.php?product_id=" . $product_id);

            exit();

        } else {

            $errors[] = "Failed to update product price.";

        }

        mysqli_stmt_close($stmtUpdate);

    }
}

?>

<div class="content">
<?php require_once __DIR__ . "/../../partials/sidebar.php"; ?>

    <div class="main-content">

        <!-- Page Header -->

        <div class="page-header">

            <div>

                <h1>Edit Product Price</h1>

                <p>Update size and price information.</p>

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

        <!-- Product Information -->

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
                        value="<?= htmlspecialchars($data['product_name']); ?>"
                        readonly>

                </div>

                <div class="form-group">

                    <label>Brand</label>

                    <input
                        type="text"
                        class="form-control"
                        value="<?= htmlspecialchars($data['brand_name']); ?>"
                        readonly>

                </div>

                <div class="form-group">

                    <label>Category</label>

                    <input
                        type="text"
                        class="form-control"
                        value="<?= htmlspecialchars($data['category_name']); ?>"
                        readonly>

                </div>

            </div>

        </div>

        <!-- Edit Price -->

        <div class="card">

            <div class="card-header">

                <h2>Edit Price</h2>

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

                        <label>Size</label>

                        <input

                            type="text"

                            name="size"

                            class="form-control"

                            maxlength="100"

                            autocomplete="off"

                            placeholder="Example : 5 Kg"

                            value="<?= htmlspecialchars($size); ?>"

                            required>

                        <small>

                            Example :
                            1 Kg,
                            5 Kg,
                            20 Kg,
                            500 ml

                        </small>

                    </div>

                    <div class="form-group">

                        <label>Price</label>

                        <input

                            type="text"

                            id="price"

                            name="price"

                            class="form-control"

                            inputmode="numeric"

                            autocomplete="off"

                            placeholder="70.000"

                            value="<?= htmlspecialchars($price_display); ?>"

                            required>

                    </div>

                    <div class="action-group">

                        <button
                            type="submit"
                            class="btn-add">Update Price
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

    function formatPrice(value)
    {
        value = value.replace(/\D/g, '');

        if (value === '') {
            return '';
        }

        return new Intl.NumberFormat('id-ID').format(value);
    }

    priceInput.addEventListener('input', function () {

        this.value = formatPrice(this.value);

    });

    </script>
</div>


<?php

mysqli_stmt_close($stmtPrice);

mysqli_close($conn);

?>


