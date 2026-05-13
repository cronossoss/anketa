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

$id = (int)($_POST['id'] ?? 0);

$name = trim($_POST['name'] ?? '');
$code = trim($_POST['code'] ?? '');

if ($id <= 0) {

    die('Pogrešan ID.');
}

if ($name === '') {

    die('Naziv je obavezan.');
}

$stmt = $conn->prepare("
    UPDATE asset_types
    SET
        name = ?,
        code = ?
    WHERE id = ?
");

$stmt->bind_param(
    "ssi",
    $name,
    $code,
    $id
);

$stmt->execute();

header(
    "Location: ../types/index.php"
);

exit;
