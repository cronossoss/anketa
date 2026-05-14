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

$intervention_type =
    trim($_POST['intervention_type']);

$description =
    trim($_POST['description']);

$parts_used =
    trim($_POST['parts_used']);

$performed_by =
    $_SESSION['employee_id'] ?? null;

/* =========================
   INSERT INTERVENTION
========================= */

$stmt = $conn->prepare("
    INSERT INTO asset_service_interventions
    (
        service_id,
        performed_by,
        intervention_type,
        description,
        parts_used
    )
    VALUES
    (
        ?,
        ?,
        ?,
        ?,
        ?
    )
");

$stmt->bind_param(
    "iisss",
    $service_id,
    $performed_by,
    $intervention_type,
    $description,
    $parts_used
);

$stmt->execute();

/* =========================
   UPDATE SERVICE STATUS
========================= */

$stmt = $conn->prepare("
    UPDATE asset_services
    SET
        status = 'repairing'
    WHERE id = ?
");

$stmt->bind_param(
    "i",
    $service_id
);

$stmt->execute();

audit_log(

    'assets_services',

    'intervention',

    $service_id,

    'Dodata servisna intervencija'
);

$_SESSION['success'] =
    'Intervencija je dodata.';

header(
    'Location: ../services/view.php?id='
        . $service_id
);

exit;
