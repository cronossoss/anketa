<?php

require_once '../../../config/init.php';
require_once '../../../helpers/audit.php';

require_login();
require_role(['admin', 'hr', 'manager']);

$id =
    (int)($_GET['id'] ?? 0);

if (!$id) {

    $_SESSION['error'] =
        'Neispravan zahtev.';

    header('Location: index.php');
    exit;
}

$userId =
    $_SESSION['user_id'];

$stmt = $conn->prepare("

    SELECT

        id,
        status

    FROM attendance_exit_passes

    WHERE id = ?

    LIMIT 1

");

$stmt->bind_param(
    'i',
    $id
);

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
        'Samo zahtevi na čekanju mogu biti obrađeni.';

    header('Location: index.php');
    exit;
}

$stmt = $conn->prepare("

    UPDATE attendance_exit_passes

    SET

        status = 'rejected',
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

audit_log(
    'attendance_exit_passes',
    'reject',
    $id,
    'Odbijena izlaznica'
);

$_SESSION['success'] =
    'Izlaznica odbijena.';

header('Location: index.php');
exit;