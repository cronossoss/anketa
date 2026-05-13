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

if ($id <= 0) {

    die('Pogrešan ID.');
}

$check = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM asset_categories
    WHERE asset_type_id = ?
");

$check->bind_param(
    "i",
    $id
);

$check->execute();

$result =
    $check
    ->get_result()
    ->fetch_assoc();

if ($result['total'] > 0) {

    die('Tip ima povezane kategorije i ne može biti obrisan.');
}

$stmt = $conn->prepare("
    DELETE FROM asset_types
    WHERE id = ?
");

$stmt->bind_param(
    "i",
    $id
);

$stmt->execute();

header(
    "Location: ../types/index.php"
);

exit;
