<?php
$current = $_SERVER['REQUEST_URI'];

function isActive($url)
{
    global $current;

    return str_contains($current, $url)
        ? 'active'
        : '';
}
?>

<!-- MOBILE TOPBAR -->

<nav class="navbar navbar-dark bg-primary d-lg-none px-3">

    <div class="d-flex justify-content-between align-items-center w-100">

        <span class="navbar-brand mb-0">
            Admin
        </span>

        <button class="btn btn-outline-light"
            type="button"
            data-bs-toggle="offcanvas"
            data-bs-target="#adminSidebar">

            <i class="bi bi-list"></i>

        </button>

    </div>

</nav>

<!-- MOBILE SIDEBAR -->

<div class="offcanvas offcanvas-start bg-primary text-white"
    tabindex="-1"
    id="adminSidebar">

    <div class="offcanvas-header border-bottom border-light">

        <h5 class="offcanvas-title">
            Admin Panel
        </h5>

        <button type="button"
            class="btn-close btn-close-white"
            data-bs-dismiss="offcanvas">
        </button>

    </div>

    <div class="offcanvas-body">

        <?php include __DIR__ . '/sidebar-menu.php'; ?>

    </div>

</div>

<!-- DESKTOP SIDEBAR -->

<div class="sidebar bg-primary text-white p-3 d-none d-lg-block">

    <h4 class="mb-4">
        Admin
    </h4>

    <?php include __DIR__ . '/sidebar-menu.php'; ?>

</div>