<?php
require_once "../config/init.php";

require_login();
?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1">

    <title>User Panel</title>

    <base href="<?= BASE_URL ?>">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        href="assets/css/user.css"
        rel="stylesheet">

</head>

<body>

    <div class="wrapper">

        <?php include "../includes/user/sidebar.php"; ?>

        <div class="main">

            <?php include "../includes/user/topbar.php"; ?>

            <div class="content p-4">