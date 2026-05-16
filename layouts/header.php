<?php require_once __DIR__ . '/../config/db.php'; ?>

<!DOCTYPE html>
<html lang="sr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Anketa sistem</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="<?= url('assets/css/app.css') ?>"

        <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

</head>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script src="https://npmcdn.com/flatpickr/dist/l10n/sr.js"></script>

<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm px-4">

        <!-- LEVO + CENTER -->

        <div class="d-flex align-items-center flex-grow-1">

            <!-- MOBILE MENU -->

            <button
                class="btn btn-outline-light d-lg-none me-2"
                type="button"
                data-bs-toggle="offcanvas"
                data-bs-target="#sidebarMenu">

                ☰

            </button>

            <div class="fw-bold text-white fs-5 mx-auto">

                <?= $pageTitle ?? 'Dashboard' ?>

            </div>

        </div>



        <!-- DESNO -->

        <div class="ms-auto d-flex align-items-center gap-3">

            <!-- USER -->

            <div class="d-flex align-items-center text-white">

                <?php

                $userPhoto =
                    url('assets/images/default-user.png');

                $photoFile =
                    __DIR__ .
                    '/../uploads/employees/' .
                    ($_SESSION['photo'] ?? '');

                if (
                    !empty($_SESSION['photo']) &&
                    file_exists($photoFile)
                ) {

                    $userPhoto =
                        url(
                            'uploads/employees/' .
                                $_SESSION['photo']
                        );
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
                href="<?= url('logout.php') ?>"
                class="btn btn-outline-light btn-sm">

                Logout

            </a>

        </div>

    </nav>


    <div class="container-fluid app-layout">
        <div class="row">