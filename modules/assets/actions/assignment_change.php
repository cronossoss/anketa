<?php

require_once $_SERVER['DOCUMENT_ROOT']
    . '/anketa/config/init.php';

require_login();

require_role(['admin', 'it']);

if (!verify_csrf($_POST['csrf_token'])) {

    die('CSRF greška.');
}

$assignment_id =
    (int) $_POST['assignment_id'];

$asset_id =
    (int) $_POST['asset_id'];

$employee_id =
    (int) $_POST['employee_id'];

$note =
    trim($_POST['note']);

$conn->begin_transaction();

/* CURRENT ASSIGNMENT */

$stmt = $conn->prepare("
    SELECT employee_id
    FROM asset_assignments
    WHERE id = ?
");

$stmt->bind_param(
    "i",
    $assignment_id
);

$stmt->execute();

$current =
    $stmt
    ->get_result()
    ->fetch_assoc();

if (
    $current
    && $current['employee_id'] == $employee_id
) {

    die('Inventar je već zadužen tom zaposlenom.');
}

try {

    $stmt = $conn->prepare("
        UPDATE asset_assignments
        SET
            returned_at = NOW()
        WHERE id = ?
    ");

    $stmt->bind_param(
        "i",
        $assignment_id
    );

    $stmt->execute();

    $stmt = $conn->prepare("
    INSERT INTO asset_assignments
    (
        asset_id,
        employee_id,
        assigned_by,
        assigned_at,
        notes
    )
    VALUES
    (
        ?,
        ?,
        ?,
        NOW(),
        ?
    )
");

    $assigned_by =
        $_SESSION['user_id'] ?? null;

    $stmt->bind_param(
        "iiis",
        $asset_id,
        $employee_id,
        $assigned_by,
        $note
    );

    $stmt->execute();

    $stmt = $conn->prepare("
    UPDATE assets
    SET status = 'zaduzen'
    WHERE id = ?
");

    $stmt->bind_param(
        "i",
        $asset_id
    );

    $stmt->execute();

    $conn->commit();

    $_SESSION['success'] =
        'Zaduženje je promenjeno.';
} catch (Exception $e) {

    $conn->rollback();

    $_SESSION['error'] =
        'Greška pri promeni zaduženja.';
}

header(
    'Location: ../assignments/index.php'
);

exit;
