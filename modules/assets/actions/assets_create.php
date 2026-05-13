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

$categoryId =
    (int) ($_POST['category_id'] ?? 0);

$typeStmt = $conn->prepare("
    SELECT asset_type_id
    FROM asset_categories
    WHERE id = ?
");

$typeStmt->bind_param(
    "i",
    $categoryId
);

$typeStmt->execute();

$typeResult =
    $typeStmt
    ->get_result()
    ->fetch_assoc();

$assetTypeId =
    (int)($typeResult['asset_type_id'] ?? 0);

$manufacturer =
    trim($_POST['manufacturer'] ?? '');

$model =
    trim($_POST['model'] ?? '');

$serialNumber =
    trim($_POST['serial_number'] ?? '');

$status =
    trim($_POST['status'] ?? 'active');

$note =
    trim($_POST['note'] ?? '');

$employeeId =
    (int) ($_POST['employee_id'] ?? 0);

if (
    empty($inventoryNumber)
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
        asset_type_id,
        category_id,
        manufacturer,
        model,
        serial_number,
        status
    )
    VALUES (?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "siissss",
    $inventoryNumber,
    $assetTypeId,
    $categoryId,
    $manufacturer,
    $model,
    $serialNumber,
    $status
);

$stmt->execute();

$assetId = $conn->insert_id;

if ($employeeId > 0) {

    $assignStmt = $conn->prepare("
        INSERT INTO asset_assignments (
            asset_id,
            employee_id,
            assigned_by
        )
        VALUES (?, ?, ?)
    ");

    $userId =
        $_SESSION['user_id'];

    $assignStmt->bind_param(
        "iii",
        $assetId,
        $employeeId,
        $userId
    );

    $assignStmt->execute();

    $statusStmt = $conn->prepare("
        UPDATE assets
        SET status = 'assigned'
        WHERE id = ?
    ");

    $statusStmt->bind_param(
        "i",
        $assetId
    );

    $statusStmt->execute();
}

$attributes =
    $_POST['attributes'] ?? [];

foreach ($attributes as $attributeId => $value) {

    if (is_array($value)) {
        continue;
    }

    $attributeId = (int)$attributeId;

    $value = trim((string)$value);

    if ($value === '') {
        continue;
    }

    $stmtAttr = $conn->prepare("
        INSERT INTO asset_attribute_values (
            asset_id,
            attribute_definition_id,
            value_text
        )
        VALUES (?, ?, ?)
    ");

    $stmtAttr->bind_param(
        "iis",
        $assetId,
        $attributeId,
        $value
    );

    $stmtAttr->execute();
}

logAudit(
    $conn,
    $_SESSION['user_id'],
    'assets',
    'create',
    $assetId,
    'Kreiran asset: ' . $inventoryNumber
);

$_SESSION['success'] =
    'Inventar uspešno dodat.';

header(
    'Location: ../assets/index.php'
);

exit;
