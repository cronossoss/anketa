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

$assetId =
    (int)($_POST['asset_id'] ?? 0);

if (!$assetId) {

    die('Neispravan ID.');
}

/* =========================
   FIND ACTIVE ASSIGNMENT
========================= */

$stmt = $conn->prepare("
    SELECT id
    FROM asset_assignments
    WHERE asset_id = ?
    AND returned_at IS NULL
    LIMIT 1
");

$stmt->bind_param(
    "i",
    $assetId
);

$stmt->execute();

$assignment =
    $stmt
        ->get_result()
        ->fetch_assoc();

if (!$assignment) {

    die('Inventar nije zadužen.');
}

/* =========================
   CLOSE ASSIGNMENT
========================= */

$updateStmt = $conn->prepare("
    UPDATE asset_assignments
    SET returned_at = NOW()
    WHERE id = ?
");

$updateStmt->bind_param(
    "i",
    $assignment['id']
);

$updateStmt->execute();

/* =========================
   UPDATE ASSET STATUS
========================= */

$statusStmt = $conn->prepare("
    UPDATE assets
    SET status = 'active'
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
    'unassign',
    $assetId,
    'Razdužen inventar'
);

/* =========================
   SUCCESS
========================= */

$_SESSION['success'] =
    'Inventar uspešno razdužen.';

header(
    'Location: ../assets/view.php?id=' . $assetId
);

exit;