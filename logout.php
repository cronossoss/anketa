<?php

require_once __DIR__ . '/config/init.php';

$_SESSION = [];
session_destroy();

redirect('index.php?logout=1');
exit;
