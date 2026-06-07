<?php

require_once '../../config/init.php';

require_login();

$pageTitle = 'Dashboard';

include "../../layouts/layout_start.php";

include "../../admin/dashboard_content.php";

include "../../layouts/layout_end.php";
