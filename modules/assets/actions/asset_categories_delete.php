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

$checkAssets = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM assets
    WHERE category_id = ?
");

$checkAssets->bind_param(
    "i",
    $id
);

$checkAssets->execute();

$assetsResult =
    $checkAssets
    ->get_result()
    ->fetch_assoc();

if ($assetsResult['total'] > 0) {

    die('Kategorija se koristi na inventaru i ne može biti obrisana.');
}

$checkAttributes = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM asset_attribute_definitions
    WHERE category_id = ?
");

$checkAttributes->bind_param(
    "i",
    $id
);

$checkAttributes->execute();

$attributesResult =
    $checkAttributes
    ->get_result()
    ->fetch_assoc();

if ($attributesResult['total'] > 0) {

    die('Kategorija ima definisane atribute i ne može biti obrisana.');
}

$stmt = $conn->prepare("
    DELETE FROM asset_categories
    WHERE id = ?
");

$stmt->bind_param(
    "i",
    $id
);

$stmt->execute();

header(
    "Location: ../categories/index.php"
);

exit;
