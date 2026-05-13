<?php

require_once $_SERVER['DOCUMENT_ROOT']
    . '/anketa/config/init.php';

require_login();

require_role(['admin', 'it']);

if (
    !verify_csrf(
        $_POST['csrf_token'] ?? ''
    )
) {
    die('CSRF greška.');
}

$assignmentId =
    (int)($_POST['assignment_id'] ?? 0);

if ($assignmentId <= 0) {

    die('Neispravno zaduženje.');
}

$stmt = $conn->prepare("
    SELECT asset_id
    FROM asset_assignments
    WHERE id = ?
");

$stmt->bind_param(
    "i",
    $assignmentId
);

$stmt->execute();

$assignment =
    $stmt
    ->get_result()
    ->fetch_assoc();

if (!$assignment) {

    die('Zaduženje nije pronađeno.');
}

$assetId =
    (int)$assignment['asset_id'];

$returnStmt = $conn->prepare("
    UPDATE asset_assignments
    SET returned_at = NOW()
    WHERE id = ?
");

$returnStmt->bind_param(
    "i",
    $assignmentId
);

$returnStmt->execute();

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

$_SESSION['success'] =
    'Inventar je razdužen.';

header(
    'Location: ../assignments/index.php'
);

exit;
