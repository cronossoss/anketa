<?php

require_once $_SERVER['DOCUMENT_ROOT']
    . '/anketa/config/init.php';

require_once $_SERVER['DOCUMENT_ROOT']
    . '/anketa/helpers/audit.php';

require_login();

require_role(['admin', 'it']);

if (!verify_csrf($_POST['csrf_token'])) {

    die('CSRF greška.');
}

$service_id =
    (int) $_POST['service_id'];

/* =========================
   SERVICE
========================= */

$stmt = $conn->prepare("
    SELECT
        id,
        asset_id,
        status
    FROM asset_services
    WHERE id = ?
");

$stmt->bind_param(
    "i",
    $service_id
);

$stmt->execute();

$service =
    $stmt
    ->get_result()
    ->fetch_assoc();

if (!$service) {

    die('Servis nije pronađen.');
}

$conn->begin_transaction();

try {

    /* COMPLETE SERVICE */

    $stmt = $conn->prepare("
        UPDATE asset_services
        SET
            status = 'completed',
            completed_at = NOW()
        WHERE id = ?
    ");

    $stmt->bind_param(
        "i",
        $service_id
    );

    $stmt->execute();

    /* RETURN ASSET */

    $stmt = $conn->prepare("
        UPDATE assets
        SET status = 'slobodan'
        WHERE id = ?
    ");

    $stmt->bind_param(
        "i",
        $service['asset_id']
    );

    $stmt->execute();

    /* STATUS HISTORY */

    $stmt = $conn->prepare("
        INSERT INTO asset_status_history
        (
            asset_id,
            old_status,
            new_status,
            changed_by,
            notes
        )
        VALUES
        (
            ?,
            'repair',
            'active',
            ?,
            ?
        )
    ");

    $changed_by =
        $_SESSION['user_id'] ?? null;

    $note =
        'Servis završen';

    $stmt->bind_param(
        "iis",
        $service['asset_id'],
        $changed_by,
        $note
    );

    $stmt->execute();

    audit_log(

        'assets_services',

        'complete',

        $service_id,

        'Servis je završen'
    );

    $conn->commit();

    $_SESSION['success'] =
        'Servis je završen.';
} catch (Exception $e) {

    $conn->rollback();

    die($e->getMessage());
}

header(
    'Location: ../services/view.php?id='
        . $service_id
);

exit;
