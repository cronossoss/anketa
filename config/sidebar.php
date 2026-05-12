<?php

return [

    [
        'title' => 'Dashboard',
        'icon'  => 'bi bi-grid',
        'url'   => 'admin/dashboard.php',
        'roles' => ['admin', 'hr', 'it', 'manager']
    ],

    [
        'title' => 'Zaposleni',
        'icon'  => 'bi bi-people',
        'url'   => 'admin/employees.php',
        'roles' => ['admin', 'hr']
    ],

    [
        'title' => 'Organizacija',
        'icon'  => 'bi bi-diagram-3',
        'url'   => 'admin/organization.php',
        'roles' => ['admin', 'hr']
    ],

    [
        'title' => 'Korisnici',
        'icon'  => 'bi bi-person-lock',
        'url'   => 'admin/users.php',
        'roles' => ['admin', 'it']
    ],

    [
        'title' => 'Ankete',
        'icon'  => 'bi bi-ui-checks',
        'url'   => 'admin/surveys.php',
        'roles' => ['admin', 'manager']
    ],

    [
        'title' => 'Izveštaji',
        'icon'  => 'bi bi-bar-chart',
        'url'   => 'admin/reports.php',
        'roles' => ['admin', 'manager']
    ]

];
