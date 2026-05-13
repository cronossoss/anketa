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
        'title' => 'Inventar',
        'icon'  => 'bi bi-pc-display',
        'page'  => 'inventory',
        'roles' => ['admin', 'it'],

        'submenu' => [

            [
                'title' => 'Pregled inventara',
                'url'   => BASE_URL . 'modules/assets/index.php',
                'page'  => 'assets-dashboard'
            ],

            [
                'title' => 'IT Inventar',
                'url'   => BASE_URL . 'modules/assets/assets/index.php',
                'page'  => 'asset-items'
            ],

            [
                'title' => 'Zaduženja',
                'url'   => BASE_URL . 'modules/assets/assignments/index.php',
                'page'  => 'asset-assignments'
            ],

            [
                'title' => 'Tipovi inventara',
                'url'   => BASE_URL . 'modules/assets/types/index.php',
                'page'  => 'asset-types'
            ],

            [
                'title' => 'Kategorije inventara',
                'url'   => BASE_URL . 'modules/assets/categories/index.php',
                'page'  => 'asset-categories'
            ],

            [
                'title' => 'Definicije atributa',
                'url'   => BASE_URL . 'modules/assets/attributes/definitions.php',
                'page'  => 'asset-attributes'
            ]

        ]
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

        <?php $isSubmenuActive = false;

        if (isset($item['submenu'])) {

            foreach ($item['submenu'] as $sub) {

                if (isActive($sub['page'])) {

                    $isSubmenuActive = true;

                    break;
                }
            }
        }
        ?>

        <li class="nav-item mb-2">

            <?php if (isset($item['submenu'])): ?>

                <a
                    class="nav-link text-white d-flex justify-content-between align-items-center"
                    data-bs-toggle="collapse"
                    href="#submenu-<?= md5($item['title']) ?>"
                    role="button">

                    <span>

                        <i class="<?= e($item['icon']) ?> me-2"></i>

                        <?= e($item['title']) ?>

                    </span>

                    <i class="bi bi-chevron-down"></i>

                </a>

                <div
                    class="collapse <?= $isSubmenuActive ? 'show' : '' ?>"
                    id="submenu-<?= md5($item['title']) ?>">

                    <ul class="nav flex-column ms-3 mt-2">

                        <?php foreach ($item['submenu'] as $sub): ?>

                            <li class="nav-item mb-1">

                                <a
                                    class="nav-link text-white small <?= isActive($sub['page']) ?>"
                                    href="<?= $sub['url'] ?>">

                                    <?= e($sub['title']) ?>

                                </a>

                            </li>

                        <?php endforeach; ?>

                    </ul>

                </div>

            <?php else: ?>

                <a
                    class="nav-link text-white <?= isActive($item['page']) ?>"
                    href="<?= $item['url'] ?>">

                    <i class="<?= e($item['icon']) ?> me-2"></i>

                    <?= e($item['title']) ?>

                </a>

            <?php endif; ?>

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