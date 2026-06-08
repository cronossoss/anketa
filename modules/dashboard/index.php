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

if (has_role(['admin', 'hr', 'it'])) {

    include "../../admin/dashboard_content.php";
} elseif (has_role(['manager'])) {

    include "partials/manager_dashboard.php";
} else {

    include "partials/user_dashboard.php";
}

include "../../layouts/layout_end.php";
