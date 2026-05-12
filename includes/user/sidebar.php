<?php
$current = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar bg-primary text-white p-3 d-none d-lg-block">

    <h4 class="mb-4">
        Korisnik
    </h4>

    <ul class="nav flex-column">

        <li class="nav-item mb-2">
            <a class="nav-link text-white <?= $current == 'dashboard.php' ? 'active' : '' ?>"
                href="user/dashboard.php">
                Dashboard
            </a>
        </li>

        <li class="nav-item mb-2">
            <a class="nav-link text-white <?= $current == 'profile.php' ? 'active' : '' ?>"
                href="user/profile.php">
                Moj profil
            </a>
        </li>

        <li class="nav-item mb-2">
            <a class="nav-link text-white"
                href="logout.php">
                Logout
            </a>
        </li>

    </ul>

</div>

<div
    class="offcanvas offcanvas-start bg-primary text-white"
    tabindex="-1"
    id="mobileSidebar">

    <div class="offcanvas-header">

        <h5 class="offcanvas-title">
            Korisnik
        </h5>

        <button
            type="button"
            class="btn-close btn-close-white"
            data-bs-dismiss="offcanvas"></button>

    </div>

    <div class="offcanvas-body">

        <ul class="nav flex-column">

            <li class="nav-item mb-2">

                <a class="nav-link text-white"
                    href="user/dashboard.php">

                    Dashboard

                </a>

            </li>

            <li class="nav-item mb-2">

                <a class="nav-link text-white"
                    href="user/profile.php">

                    Moj profil

                </a>

            </li>

            <li class="nav-item mb-2">

                <a class="nav-link text-white"
                    href="logout.php">

                    Logout

                </a>

            </li>

        </ul>

    </div>

</div>