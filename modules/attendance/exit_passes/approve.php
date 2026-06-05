<?php

require_once '../../../config/init.php';

require_login();
require_role([
    'admin',
    'hr',
    'manager'
]);

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
    SELECT
        id,
        status
    FROM attendance_exit_passes
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param('i', $id);
$stmt->execute();

$pass =
    $stmt
        ->get_result()
        ->fetch_assoc();

if (!$pass) {

    $_SESSION['error'] =
        'Izlaznica nije pronađena.';

    header('Location: index.php');
    exit;
}

if ($pass['status'] !== 'pending') {

    $_SESSION['error'] =
        'Izlaznica je već obrađena.';

    header('Location: index.php');
    exit;
}

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

audit_log(
    'attendance_exit_passes',
    'approve',
    $id,
    'Odobrena izlaznica'
);

header(
    'Location: index.php'
);

exit;