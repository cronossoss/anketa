<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once "../../../config/db.php";

require_once "../../../helpers/auth.php";
require_once "../../../helpers/csrf.php";
require_once "../../../helpers/helpers.php";
require_once "../../../helpers/audit.php";

require_login();

require_role(['admin', 'it']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit;
}

if (!verify_csrf($_POST['csrf'] ?? '')) {
    exit('CSRF');
}

$name = trim($_POST['name'] ?? '');

$code = trim($_POST['code'] ?? '');

if (empty($name)) {
    exit('Naziv je obavezan');
}

$stmt = $conn->prepare("
    INSERT INTO asset_categories
    (
        name,
        code
    )
    VALUES
    (?, ?)
");

$stmt->bind_param(
    "ss",
    $name,
    $code
);

$stmt->execute();

$newId = $conn->insert_id;

audit_log(
    'asset_categories',
    'create',
    $newId,
    'Kreiran asset kategorija: ' . $name
);

header(
    "Location: /anketa/modules/assets/categories/index.php"
);

exit;
