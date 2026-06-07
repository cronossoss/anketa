<?php

require_once '../config/init.php';

require_login();

header(
    'Location: ' .
        url('modules/dashboard/index.php')
);

exit;
