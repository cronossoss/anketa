<?php

require_once __DIR__ . '/../../helpers/permissions.php';

function isActive(string $page): string
{
    global $currentPage;

    return (
        isset($currentPage)
        && $currentPage === $page
    )
        ? 'active'
        : '';
}

$menuSections = [];

/*
|--------------------------------------------------------------------------
| MOJ PORTAL
|--------------------------------------------------------------------------
*/

if (isEmployeeUser()) {

    $menuSections['MOJ PORTAL'] = [

        [
            'title' => 'Dashboard',
            'icon'  => 'bi bi-speedometer2',
            'url'   => url('modules/dashboard/index.php'),
            'page'  => 'dashboard.php'
        ],

        [
            'title' => 'Moje prisustvo',
            'icon'  => 'bi bi-calendar-check',
            'url'   => url('modules/attendance/my_attendance/index.php'),
            'page'  => 'my-attendance'
        ],

        [
            'title' => 'Moji zahtevi',
            'icon'  => 'bi bi-send',
            'url'   => url('modules/attendance/requests/index.php'),
            'page'  => 'attendance-requests'
        ]
    ];
}

/*
|--------------------------------------------------------------------------
| RUKOVOĐENJE
|--------------------------------------------------------------------------
*/

if (canApproveRequests()) {

    $menuSections['RUKOVOĐENJE'] = [

        [
            'title' => 'Zahtevi zaposlenih',
            'icon'  => 'bi bi-check2-square',
            'url'   => url('modules/attendance/approval_requests/index.php'),
            'page'  => 'approval-requests'
        ],

        [
            'title' => 'Moja OJ',
            'icon'  => 'bi bi-diagram-2',
            'url'   => url('modules/attendance/my_ou/index.php'),
            'page'  => 'my-ou-dashboard'
        ]
    ];
}

/*
|--------------------------------------------------------------------------
| KADROVI
|--------------------------------------------------------------------------
*/

if (canAccessOrganization()) {

    $menuSections['KADROVI'] = [

        [
            'title' => 'Organizacija',
            'icon'  => 'bi bi-diagram-3',
            'url'   => url('admin/organization.php'),
            'page'  => 'organization.php'
        ],

        [
            'title' => 'Zaposleni',
            'icon'  => 'bi bi-people',
            'url'   => url('admin/employees.php'),
            'page'  => 'employees.php'
        ]
    ];
}

/*
|--------------------------------------------------------------------------
| PRISUSTVO
|--------------------------------------------------------------------------
*/

if (canAccessAttendanceAdmin()) {

    $menuSections['PRISUSTVO'] = [

        [
            'title' => 'Dashboard',
            'icon'  => 'bi bi-speedometer',
            'url'   => url('modules/attendance/dashboard.php'),
            'page'  => 'attendance-dashboard'
        ],

        [
            'title' => 'Prisutnost',
            'icon'  => 'bi bi-person-check',
            'url'   => url('modules/attendance/present.php'),
            'page'  => 'attendance-present'
        ],

        [
            'title' => 'Trenutno stanje',
            'icon'  => 'bi bi-clock-history',
            'url'   => url('modules/attendance/current_status.php'),
            'page'  => 'attendance-current-status'
        ],

        [
            'title' => 'Odsustva',
            'icon'  => 'bi bi-calendar-x',
            'url'   => url('modules/attendance/absences/index.php'),
            'page'  => 'attendance-absences'
        ],

        [
            'title' => 'Izlaznice',
            'icon'  => 'bi bi-door-open',
            'url'   => url('modules/attendance/exit_passes/index.php'),
            'page'  => 'attendance-exit-passes'
        ],

        [
            'title' => 'Rasporedi',
            'icon'  => 'bi bi-calendar3',
            'url'   => url('modules/attendance/schedules/index.php'),
            'page'  => 'attendance-schedules'
        ],

        [
            'title' => 'Vrste odsustva',
            'icon'  => 'bi bi-tags',
            'url'   => url('modules/attendance/absence-types/index.php'),
            'page'  => 'attendance-absence-types'
        ]
    ];
}

/*
|--------------------------------------------------------------------------
| IT INVENTAR
|--------------------------------------------------------------------------
*/

if (canAccessInventory()) {

    $menuSections['IT INVENTAR'] = [

        [
            'title' => 'Pregled inventara',
            'icon'  => 'bi bi-grid',
            'url'   => url('modules/assets/index.php'),
            'page'  => 'assets-dashboard'
        ],

        [
            'title' => 'IT Inventar',
            'icon'  => 'bi bi-pc-display',
            'url'   => url('modules/assets/assets/index.php'),
            'page'  => 'asset-items'
        ],

        [
            'title' => 'Zaduženja',
            'icon'  => 'bi bi-person-workspace',
            'url'   => url('modules/assets/assignments/index.php'),
            'page'  => 'asset-assignments'
        ],

        [
            'title' => 'Servisi',
            'icon'  => 'bi bi-tools',
            'url'   => url('modules/assets/services/index.php'),
            'page'  => 'asset-services'
        ],

        [
            'title' => 'Tipovi inventara',
            'icon'  => 'bi bi-collection',
            'url'   => url('modules/assets/types/index.php'),
            'page'  => 'asset-types'
        ],

        [
            'title' => 'Kategorije inventara',
            'icon'  => 'bi bi-folder',
            'url'   => url('modules/assets/categories/index.php'),
            'page'  => 'asset-categories'
        ]
    ];
}

/*
|--------------------------------------------------------------------------
| ADMINISTRACIJA
|--------------------------------------------------------------------------
*/

if (canAccessAudit()) {

    $menuSections['ADMINISTRACIJA'] = [

        [
            'title' => 'Korisnici',
            'icon'  => 'bi bi-person-badge',
            'url'   => url('admin/users.php'),
            'page'  => 'users.php'
        ],

        [
            'title' => 'Audit log',
            'icon'  => 'bi bi-clock-history',
            'url'   => url('admin/audit_logs.php'),
            'page'  => 'audit_logs.php'
        ]
    ];
}
