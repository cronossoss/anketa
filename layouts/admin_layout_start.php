<?php
require_once __DIR__ . "/../config/init.php";

require_login();
require_admin();
?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1">

    <title>Admin Panel</title>

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link
        href="<?= BASE_URL ?>assets/css/admin.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="<?= BASE_URL ?>assets/css/modules/organization.css">

    <script>
        const APP = {
            baseUrl: <?= json_encode(BASE_URL) ?>,
            csrfToken: <?= json_encode(csrf_token()) ?>
        };
    </script>

</head>

<body>

    <div class="wrapper">

        <?php include __DIR__ . "/../includes/admin/sidebar.php"; ?>

        <div class="main">

            <?php include __DIR__ . "/../includes/admin/topbar.php"; ?>

            <div class="content p-4">