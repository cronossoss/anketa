<?php
$current = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar bg-primary text-white p-3 d-none d-lg-block">

    <h4 class="mb-4">
        Korisnik
    </h4>

    <ul class="nav flex-column">

        <li class="nav-item mb-2">

            <a
                class="nav-link text-white <?= $current == 'dashboard.php' ? 'active' : '' ?>"
                href="<?= url('user/dashboard.php') ?>">

                <i class="bi bi-speedometer2 me-2"></i>

                Dashboard

            </a>

        </li>

        <li class="nav-item mb-2">

            <a
                class="nav-link text-white <?= $current == 'profile.php' ? 'active' : '' ?>"
                href="<?= url('user/profile.php') ?>">

                <i class="bi bi-person me-2"></i>

                Moj profil

            </a>

        </li>

        <li class="nav-item mb-2">

            <a
                class="nav-link text-white"
                href="<?= url('modules/attendance/my_attendance/index.php') ?>">

                <i class="bi bi-calendar-check me-2"></i>

                Moje prisustvo

            </a>

        </li>

        <li class="nav-item mb-2">

            <a
                class="nav-link text-white"
                href="<?= url('modules/attendance/employee_exit_passes/index.php') ?>">

                <i class="bi bi-door-open me-2"></i>

                Moje izlaznice

            </a>

        </li>

        <li class="nav-item mb-2">

            <a
                class="nav-link text-white"
                href="<?= url('modules/attendance/employee_absences/index.php') ?>">

                <i class="bi bi-calendar-x me-2"></i>

                Moja odsustva

            </a>

        </li>

        <li class="nav-item mt-3">

            <a
                class="nav-link text-white"
                href="<?= url('logout.php') ?>">

                <i class="bi bi-box-arrow-right me-2"></i>

                Logout

            </a>

        </li>

    </ul>