<?php

require_once "../../../config/init.php";

require_login();

require_role(['admin', 'it']);

if (
    !verify_csrf(
        $_POST['csrf_token'] ?? ''
    )
) {

    die('CSRF greška.');
}

$name = trim($_POST['name'] ?? '');
$code = trim($_POST['code'] ?? '');

if ($name === '') {

    die('Naziv je obavezan.');
}

$stmt = $conn->prepare("
    INSERT INTO asset_types (
        name,
        code
    )
    VALUES (?, ?)
");

$stmt->bind_param(
    "ss",
    $name,
    $code
);

$stmt->execute();

header(
    "Location: ../types/index.php"
);

exit;
