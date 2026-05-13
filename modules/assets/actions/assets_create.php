<?php

require_once $_SERVER['DOCUMENT_ROOT']
    . '/anketa/config/init.php';
require_once '../helpers/permissions.php';
require_once '../helpers/audit.php';

require_Login();

if (!hasRole(['admin', 'it'])) {
    die('Nemate dozvolu.');
}

$inventoryNumber =
    trim($_POST['inventory_number'] ?? '');

$name =
    trim($_POST['name'] ?? '');

$assetTypeId =
    (int) ($_POST['asset_type_id'] ?? 0);

$categoryId =
    (int) ($_POST['category_id'] ?? 0);

if (
    empty($inventoryNumber)
    || empty($name)
    || !$assetTypeId
    || !$categoryId
) {
    die('Popunite obavezna polja.');
}

$stmt = $conn->prepare("
    SELECT id
    FROM assets
    WHERE inventory_number = ?
");

$stmt->execute([
    $inventoryNumber
]);

if ($stmt->fetch()) {
    die('Inventarski broj već postoji.');
}

$stmt = $conn->prepare("
    INSERT INTO assets (
        inventory_number,
        name,
        asset_type_id,
        category_id
    )
    VALUES (?, ?, ?, ?)
");

$stmt->execute([
    $inventoryNumber,
    $name,
    $assetTypeId,
    $categoryId
]);

$assetId = $conn->insert_id;

logAudit(
    $conn,
    $_SESSION['user_id'],
    'assets',
    'create',
    $assetId,
    'Kreiran asset: ' . $name
);

header(
    'Location: ../items/view.php?id='
    . $assetId
);

exit;