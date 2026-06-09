<?php

require_once '../../config/init.php';

require_login();



$pageTitle = 'Dashboard';

include "../../layouts/layout_start.php";

/*
|--------------------------------------------------------------------------
| DASHBOARD BY ROLE
|--------------------------------------------------------------------------
*/

switch ($_SESSION['role']) {

    case 'admin':

        include "partials/admin_dashboard.php";
        break;

    case 'hr':

        include "partials/hr_dashboard.php";
        break;

    case 'it':

        include "partials/it_dashboard.php";
        break;

    case 'manager':

        include "partials/manager_dashboard.php";
        break;

    default:

        include "partials/user_dashboard.php";
        break;
}

include "../../layouts/layout_end.php";
