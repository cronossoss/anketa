<?php

ob_start();

session_start();

mysqli_report(
    MYSQLI_REPORT_ERROR |
        MYSQLI_REPORT_STRICT
);

require_once __DIR__ . "/db.php";

require_once __DIR__ . "/../helpers/auth.php";
require_once __DIR__ . "/../helpers/csrf.php";
require_once __DIR__ . "/../helpers/helpers.php";

date_default_timezone_set('Europe/Belgrade');
