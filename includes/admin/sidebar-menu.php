<?php

$menu = [

    [
        'title' => 'Dashboard',
        'icon'  => 'bi bi-speedometer2',
        'page'  => 'dashboard.php',
        'url'   => BASE_URL . 'admin/dashboard.php',
        'roles' => ['admin', 'hr', 'it', 'manager']
    ],

    [
        'title' => 'Organizacija',
        'icon'  => 'bi bi-diagram-3',
        'page'  => 'organization.php',
        'url'   => BASE_URL . 'admin/organization.php',
        'roles' => ['admin', 'hr']
    ],

    [
        'title' => 'Zaposleni',
        'icon'  => 'bi bi-people',
        'page'  => 'employees.php',
        'url'   => BASE_URL . 'admin/employees.php',
        'roles' => ['admin', 'hr']
    ],

    [
        'title' => 'Korisnici',
        'icon'  => 'bi bi-person-badge',
        'page'  => 'users.php',
        'url'   => BASE_URL . 'admin/users.php',
        'roles' => ['admin', 'it']
    ],

    [
        'title' => 'Tipovi inventara',
        'icon'  => 'bi bi-tags',
        'page' => 'asset-types',
        'url'   => BASE_URL . 'modules/assets/types/index.php',
        'roles' => ['admin', 'it']
    ],

    [
        'title' => 'Kategorije inventara',
        'icon'  => 'bi bi-list-task',
        'page'  => 'asset-categories',
        'url'   => BASE_URL . 'modules/assets/categories/index.php',
        'roles' => ['admin', 'it']
    ],

    [
        'title' => 'Inventar',
        'icon'  => 'bi bi-pc-display',
        'page' => 'asset-items',
        'url'   => BASE_URL . 'modules/assets/items/index.php',
        'roles' => ['admin', 'it']
    ],


    [
        'title' => 'Audit log',
        'icon'  => 'bi bi-clock-history',
        'page'  => 'audit_logs.php',
        'url'   => BASE_URL . 'admin/audit_logs.php',
        'roles' => ['admin', 'it']
    ],

    [
        'title' => 'Ankete',
        'icon'  => 'bi bi-ui-checks',
        'page'  => 'surveys.php',
        'url'   => BASE_URL . 'admin/surveys.php',
        'roles' => ['admin', 'manager']
    ],

    [
        'title' => 'Izveštaji',
        'icon'  => 'bi bi-bar-chart',
        'page'  => 'reports.php',
        'url'   => BASE_URL . 'admin/reports.php',
        'roles' => ['admin', 'manager']
    ]



];
?>

<ul class="nav flex-column">

    <?php foreach ($menu as $item): ?>

        <?php if (!has_role($item['roles'])) continue; ?>

        <li class="nav-item mb-2">

            <a
                class="nav-link text-white <?= isActive($item['page']) ?>"
                href="<?= $item['url'] ?>">

                <i class="<?= e($item['icon']) ?> me-2"></i>

                <?= e($item['title']) ?>

            </a>

        </li>

    <?php endforeach; ?>

    <li class="nav-item mt-3">

        <a
            class="nav-link text-white"
            href="<?= BASE_URL ?>logout.php">

            <i class="bi bi-box-arrow-right me-2"></i>

            Logout

        </a>

    </li>

</ul>