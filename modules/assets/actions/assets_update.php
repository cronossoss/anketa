<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once dirname(__DIR__, 3)
    . '/config/init.php';

require_once dirname(__DIR__)
    . '/helpers/permissions.php';

require_once dirname(__DIR__)
    . '/helpers/audit.php';
    
require_login();

require_role(['admin', 'it']);

/* =========================
   INPUT
========================= */

$id =
    (int)($_POST['id'] ?? 0);

$name =
    trim($_POST['name'] ?? '');

$inventoryNumber =
    trim($_POST['inventory_number'] ?? '');

$manufacturer =
    trim($_POST['manufacturer'] ?? '');

$model =
    trim($_POST['model'] ?? '');

$serialNumber =
    trim($_POST['serial_number'] ?? '');

$status =
    trim($_POST['status'] ?? 'active');

/* =========================
   VALIDATION
========================= */

if (
    !$id
    || empty($name)
    || empty($inventoryNumber)
) {

    die('Popunite obavezna polja.');
}

/* =========================
   DUPLICATE CHECK
========================= */

$checkStmt = $conn->prepare("
    SELECT id
    FROM assets
    WHERE inventory_number = ?
    AND id != ?
");

$checkStmt->bind_param(
    "si",
    $inventoryNumber,
    $id
);

$checkStmt->execute();

$duplicate =
    $checkStmt
        ->get_result();

if ($duplicate->num_rows > 0) {

    die('Inventarski broj već postoji.');
}

/* =========================
   UPDATE
========================= */

$stmt = $conn->prepare("
    UPDATE assets
    SET
        name = ?,
        inventory_number = ?,
        manufacturer = ?,
        model = ?,
        serial_number = ?,
        status = ?
    WHERE id = ?
");

$stmt->bind_param(
    "ssssssi",
    $name,
    $inventoryNumber,
    $manufacturer,
    $model,
    $serialNumber,
    $status,
    $id
);

$stmt->execute();

/* =========================
   AUDIT
========================= */

logAudit(
    $conn,
    $_SESSION['user_id'],
    'assets',
    'update',
    $id,
    'Izmenjen inventar: ' . $inventoryNumber
);

/* =========================
   SUCCESS
========================= */

$_SESSION['success'] =
    'Inventar uspešno izmenjen.';

header(
    'Location: ../assets/view.php?id=' . $id
);

exit;