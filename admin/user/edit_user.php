<?php

require_once __DIR__ . "/../security.php";
require_once __DIR__ . "/../../config/connection.php";

$errors = [];
$name = '';
$role = '';


if (!$category_id) {
    header("Location: ../categories.php");
    exit;
}

if (isset($_POST['save'])) {

    $name     = trim($_POST['name'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = trim($_POST['role'] ?? '');

    if ($name === '') {
        $errors[] = 'User name is required.';
    }

    if ($password === '') {
        $errors[] = 'Password is required.';
    }

    if ($role === '') {
        $errors[] = 'User role is required.';
    }

    if (empty($errors)) {

        $sql = "
            INSERT INTO users
            (
                name, password, role, created_at
            )
            VALUES
            (
                ?, MD5(?), ?, NOW()
            )
        ";

        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                "ssi",
                $name,
                $password,
                $role
            );

            if (mysqli_stmt_execute($stmt)) {   
                mysqli_stmt_close($stmt);
                header("Location: users.php?success=added");
                exit;
            }

            $errors[] = 'Data failed to save.';
            mysqli_stmt_close($stmt);
        } else {
            $errors[] = 'Query failed to prepare.';
        }
    }
}