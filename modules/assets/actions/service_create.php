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

$asset_id =
    (int) $_POST['asset_id'];

$service_type =
    trim($_POST['service_type']);

$title =
    trim($_POST['title']);

$description =
    trim($_POST['description']);

$received_by =
    $_SESSION['user_id'] ?? null;

$user_id =
    $_SESSION['user_id'] ?? 0;

$stmt = $conn->prepare("
    SELECT employee_id
    FROM users
    WHERE id = ?
");

$stmt->bind_param(
    "i",
    $user_id
);

$stmt->execute();

$user =
    $stmt
    ->get_result()
    ->fetch_assoc();

$technician_id =
    $user['employee_id'] ?? null;

$conn->begin_transaction();

try {

    /* SERVICE */

    $stmt = $conn->prepare("
    INSERT INTO asset_services
    (
        asset_id,
        service_type,
        technician_id,
        title,
        description,
        received_by
    )
    VALUES
    (
        ?,
        ?,
        ?,
        ?,
        ?,
        ?
    )
");

    $stmt->bind_param(
        "isissi",
        $asset_id,
        $service_type,
        $technician_id,
        $title,
        $description,
        $received_by
    );

    $stmt->execute();

    $serviceId =
        $conn->insert_id;

    audit_log(

        'assets_services',

        'create',

        $serviceId,

        "Otvoren servis za asset ID {$asset_id}"
    );

    /* STATUS */

    $stmt = $conn->prepare("
        UPDATE assets
        SET status = 'servis'
        WHERE id = ?
    ");

    $stmt->bind_param(
        "i",
        $asset_id
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
            'assigned',
            'repair',
            ?,
            ?
        )
    ");

    $note =
        'Prijem u servis';

    $stmt->bind_param(
        "iis",
        $asset_id,
        $received_by,
        $note
    );

    $stmt->execute();

    $conn->commit();

    $_SESSION['success'] =
        'Inventar je poslat u servis.';
} catch (Exception $e) {

    $conn->rollback();

    die($e->getMessage());
}

header(
    'Location: ../services/index.php'
);

exit;
