<?php require_once __DIR__ . '/../config/db.php'; ?>

<!DOCTYPE html>
<html lang="sr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Anketa sistem</title>

    <base href="<?= BASE_URL ?>">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="<?= BASE_URL ?>assets/css/app.css">

</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm px-3">

    <!-- LEVO -->

    <div class="d-flex align-items-center">

        <!-- MOBILE MENU -->

        <button
            class="btn btn-outline-light d-lg-none me-2"
            type="button"
            data-bs-toggle="offcanvas"
            data-bs-target="#sidebarMenu">

            ☰

        </button>

        <!-- DASHBOARD -->

        <a
            href="<?= BASE_URL ?>admin/dashboard.php"
            class="navbar-brand fw-bold mb-0">

            Dashboard

        </a>

    </div>



    <!-- DESNO -->

    <div class="ms-auto d-flex align-items-center gap-3">

        <!-- USER -->

        <div class="d-flex align-items-center text-white">

            <?php

            $userPhoto = BASE_URL . 'assets/images/default-user.png';

                $photoFile =
                    $_SERVER['DOCUMENT_ROOT'] .
                    '../uploads/employees/' .
                    $_SESSION['photo'];
                
                if (
                    !empty($_SESSION['photo']) &&
                    file_exists($photoFile)
                ) {
                
                    $userPhoto =
                        BASE_URL .
                        '../uploads/employees/' .
                        $_SESSION['photo'];
                }

            ?>

            <img
                src="<?= $userPhoto ?>"
                alt="User"
                class="user-avatar me-2"
                width="36"
                height="36"
                style="object-fit: cover;">

            <div class="d-none d-md-block">

                <div class="small text-white-50">
                    Prijavljen
                </div>

                <div class="fw-semibold">
                    <?= e($_SESSION['name'] ?? 'Korisnik') ?>
                </div>

            </div>

        </div>


        <!-- LOGOUT -->

        <a
            href="<?= BASE_URL ?>logout.php"
            class="btn btn-outline-light btn-sm">

            Logout

        </a>

    </div>

</nav>


<div class="container-fluid app-layout">
    <div class="row">