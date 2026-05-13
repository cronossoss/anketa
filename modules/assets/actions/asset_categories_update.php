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

$assetTypeId =
    (int)($_POST['asset_type_id'] ?? 0);

$name = trim($_POST['name'] ?? '');

$code = trim($_POST['code'] ?? '');

$stmt = $conn->prepare("
    UPDATE asset_categories
    SET
        asset_type_id = ?,
        name = ?,
        code = ?
    WHERE id = ?
");

$stmt->bind_param(
    "issi",
    $assetTypeId,
    $name,
    $code,
    $id
);

$stmt->execute();

header(
    "Location: ../categories/index.php"
);

exit;
