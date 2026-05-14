<?php

require_once dirname(__DIR__, 3)
    . '/config/init.php';

require_once dirname(__DIR__)
    . '/helpers/permissions.php';

require_once dirname(__DIR__)
    . '/helpers/audit.php';

require_Login();

if (!hasRole(['admin', 'it'])) {

    die('Nemate dozvolu.');
}

/* =========================
   INPUT
========================= */

$assetId =
    (int)($_POST['asset_id'] ?? 0);

$employeeId =
    (int)($_POST['employee_id'] ?? 0);

if (
    !$assetId
    || !$employeeId
) {

    die('Popunite obavezna polja.');
}

/* =========================
   CHECK ASSET
========================= */

$assetStmt = $conn->prepare("
    SELECT id, name
    FROM assets
    WHERE id = ?
");

$assetStmt->bind_param(
    "i",
    $assetId
);

$assetStmt->execute();

$asset =
    $assetStmt
        ->get_result()
        ->fetch_assoc();

if (!$asset) {

    die('Inventar nije pronađen.');
}

/* =========================
   CHECK ACTIVE ASSIGNMENT
========================= */

$checkStmt = $conn->prepare("
    SELECT id
    FROM asset_assignments
    WHERE asset_id = ?
    AND returned_at IS NULL
    LIMIT 1
");

$checkStmt->bind_param(
    "i",
    $assetId
);

$checkStmt->execute();

$activeAssignment =
    $checkStmt
        ->get_result()
        ->fetch_assoc();

if ($activeAssignment) {

    die('Inventar je već zadužen.');
}

/* =========================
   CREATE ASSIGNMENT
========================= */

$stmt = $conn->prepare("
    INSERT INTO asset_assignments (
        asset_id,
        employee_id,
        assigned_by
    )
    VALUES (?, ?, ?)
");

$userId =
    $_SESSION['user_id'];

$stmt->bind_param(
    "iii",
    $assetId,
    $employeeId,
    $userId
);

$stmt->execute();

/* =========================
   UPDATE STATUS
========================= */

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

/* =========================
   AUDIT
========================= */

logAudit(
    $conn,
    $_SESSION['user_id'],
    'assets',
    'assign',
    $assetId,
    'Zadužen inventar: ' . $asset['name']
);

/* =========================
   SUCCESS
========================= */

$_SESSION['success'] =
    'Inventar uspešno zadužen.';

header(
    'Location: ../assets/index.php'
);

exit;