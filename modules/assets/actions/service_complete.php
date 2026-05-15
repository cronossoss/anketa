<?php

require_once '../../../config/init.php';
require_once dirname(__DIR__) . '/helpers/audit.php';

require_login();

require_role(['admin', 'it']);

$serviceId =
    (int) ($_POST['service_id'] ?? 0);

$resolution =
    trim($_POST['resolution'] ?? 'return');

$note =
    trim($_POST['note'] ?? '');

if (!$serviceId) {

    die('Neispravan servis.');
}

/* =========================
   SERVICE
========================= */

$serviceResult = $conn->query("
    SELECT *
    FROM asset_services
    WHERE id = {$serviceId}
    LIMIT 1
");

$service =
    $serviceResult->fetch_assoc();

if (!$service) {

    die('Servis nije pronađen.');
}

$assetId =
    (int) $service['asset_id'];

/* =========================
   ACTIVE ASSIGNMENT
========================= */

$assignmentResult = $conn->query("
    SELECT *
    FROM asset_assignments
    WHERE asset_id = {$assetId}
    AND returned_at IS NULL
    ORDER BY id DESC
    LIMIT 1
");

$assignment =
    $assignmentResult->fetch_assoc();

/* =========================
   COMPLETE SERVICE
========================= */

$noteEscaped =
    $conn->real_escape_string($note);

$conn->query("
    UPDATE asset_services
    SET
        status = 'completed',
        completed_at = NOW(),
        notes = CONCAT(
            COALESCE(notes, ''),
            '\n\n',
            'Završetak: {$noteEscaped}'
        )
    WHERE id = {$serviceId}
");

/* =========================
   RETURN TO USER
========================= */

if ($resolution === 'return') {

    $conn->query("
        UPDATE assets
        SET status = 'zaduzen'
        WHERE id = {$assetId}
    ");
}

/* =========================
   UNASSIGN
========================= */

elseif ($resolution === 'unassign') {

    if ($assignment) {

        $assignmentId =
            (int) $assignment['id'];

        $conn->query("
            UPDATE asset_assignments
            SET returned_at = NOW()
            WHERE id = {$assignmentId}
        ");
    }

    $conn->query("
        UPDATE assets
        SET status = 'slobodno'
        WHERE id = {$assetId}
    ");
}

/* =========================
   DISPOSE
========================= */

elseif ($resolution === 'dispose') {

    if ($assignment) {

        $assignmentId =
            (int) $assignment['id'];

        $conn->query("
            UPDATE asset_assignments
            SET returned_at = NOW()
            WHERE id = {$assignmentId}
        ");
    }

    $conn->query("
        UPDATE assets
        SET
            status = 'rashodovan',
            disposed_at = NOW(),
            disposal_reason = '{$noteEscaped}'
        WHERE id = {$assetId}
    ");
}

/* =========================
   AUDIT
========================= */

logAudit(
    $conn,
    $_SESSION['user_id'],
    'services',
    'complete',
    $serviceId,
    'Završen servis uređaja ID: ' . $assetId
);

$_SESSION['success'] =
    'Servis uspešno završen.';

header(
    'Location: ../services/index.php'
);

exit;