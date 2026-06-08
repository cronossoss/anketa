<?php
require_once __DIR__ . "/../config/init.php";

require_login();
if (
    !in_array(
        $_SESSION['role'] ?? '',
        ['admin', 'hr', 'manager', 'it', 'user']
    )
) {
    die('Access denied');
}
?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1">

    <title><?= $pageTitle ?> | IIS</title>

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
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link
        href="<?= url('assets/css/admin.css') ?>"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="<?= url('assets/css/modules/organization.css') ?>">

    <link
        rel="stylesheet"
        href="<?= url('assets/css/modules/employees/employee-view.css') ?>">

    <script>
        const APP = {
            baseUrl: <?= json_encode(BASE_URL) ?>,
            csrfToken: <?= json_encode(csrf_token()) ?>
        };
    </script>

</head>

<body>

    <div class="wrapper">

        <?php include __DIR__ . "/../includes/layout/sidebar.php"; ?>

        <?php include __DIR__ . "/../includes/layout/mobile_sidebar.php"; ?>

        <div class="main">

            <?php include __DIR__ . "/../includes/layout/topbar.php"; ?>

            <div class="content p-4"></div>