<?php
require_once "config/db.php";

require_once "helpers/auth.php";
require_once "helpers/csrf.php";
require_once "helpers/helpers.php";

$_SESSION = [];
session_destroy();

header("Location: " . BASE_URL . "index.php?logout=1");
exit;
