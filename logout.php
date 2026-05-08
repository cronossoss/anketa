<?php require_once "config/db.php";

$_SESSION = [];
session_destroy();

header("Location: " . BASE_URL . "index.php?logout=1");
exit;
