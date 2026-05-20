<?php
$current = basename($_SERVER['PHP_SELF']);

function isActive($identifier): string
{
    $uri = $_SERVER['REQUEST_URI'];

    $map = [

        'dashboard.php' =>
        app_path('admin/dashboard.php'),

        'organization.php' =>
        app_path('admin/organization.php'),

        'employees.php' =>
        app_path('admin/employees.php'),

        'attendance-dashboard' =>
        app_path('modules/attendance/index.php'),

        'attendance-present.php' =>
        app_path('modules/attendance/present.php'),

        'attendance.php' =>
        app_path('modules/attendance/schedules/index.php'),

        'users.php' =>
        app_path('admin/users.php'),

        'assets-dashboard' =>
        app_path('modules/assets/index.php'),

        'asset-types' =>
        app_path('modules/assets/types/'),

        'asset-categories' =>
        app_path('modules/assets/categories/'),

        'asset-attributes' =>
        app_path('modules/assets/attributes/'),

        'asset-items' =>
        app_path('modules/assets/assets/'),

        'asset-assignments' =>
        app_path('modules/assets/assignments/'),

        'asset-services' =>
        app_path('modules/assets/services/'),

        'audit_logs.php' =>
        app_path('admin/audit_logs.php'),

        'surveys.php' =>
        app_path('admin/surveys.php'),

        'reports.php' =>
        app_path('admin/reports.php'),
    ];
    if (
        isset($map[$identifier])
        && strpos($uri, $map[$identifier]) !== false
    ) {
        return 'active';
    }

    return '';
}
?>

<!-- MOBILE TOPBAR -->

<nav class="navbar navbar-dark bg-primary d-lg-none px-3">

    <div class="d-flex justify-content-between align-items-center w-100">

        <span class="navbar-brand mb-0">
            Admin
        </span>

        <button
            class="btn btn-outline-light"
            type="button"
            data-bs-toggle="offcanvas"
            data-bs-target="#mobileSidebar">

            <i class="bi bi-list"></i>

        </button>

    </div>

</nav>

<!-- DESKTOP SIDEBAR -->

<div class="sidebar bg-primary text-white p-3 d-none d-lg-block">

    <h4 class="mb-4">
        Admin
    </h4>

    <?php include __DIR__ . '/sidebar-menu.php'; ?>

</div>

<!-- MOBILE SIDEBAR -->

<div
    class="offcanvas offcanvas-start bg-primary text-white"
    tabindex="-1"
    id="mobileSidebar">

    <div class="offcanvas-header">

        <h5 class="offcanvas-title">
            Admin
        </h5>

        <button
            type="button"
            class="btn-close btn-close-white"
            data-bs-dismiss="offcanvas">
        </button>

    </div>

    <div class="offcanvas-body">

        <?php include __DIR__ . '/sidebar-menu.php'; ?>

    </div>

</div>