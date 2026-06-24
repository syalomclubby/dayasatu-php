<?php

require_once __DIR__ . "/../security.php";
require_once __DIR__ . "/../../config/connection.php";

$user_id = filter_input(INPUT_GET, 'user_id', FILTER_VALIDATE_INT);

if (!$user_id) {
    header("Location: users.php");
    exit;
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
