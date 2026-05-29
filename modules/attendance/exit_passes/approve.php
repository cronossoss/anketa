<?php

require_once '../../../config/init.php';

require_login();

$id =
    (int)($_GET['id'] ?? 0);

if (!$id) {

    header(
        'Location: index.php'
    );

    exit;
}

$userId =
    $_SESSION['user_id']
    ?? null;

$stmt = $conn->prepare("

    UPDATE attendance_exit_passes

    SET

        status = 'approved',

        approved_by = ?,

        approved_at = NOW()

    WHERE id = ?

");

$stmt->bind_param(

    'ii',

    $userId,
    $id

);

$stmt->execute();

$_SESSION['success'] =
    'Izlaznica odobrena.';

header(
    'Location: index.php'
);

exit;