<?php

require_once '../../../config/init.php';
require_once '../../../helpers/audit.php';

require_login();
require_role(['admin', 'hr', 'manager']);

$id = (int)($_GET['id'] ?? 0);

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
        employee_id,
        status

    FROM attendance_absences

    WHERE id = ?

    LIMIT 1

");

$stmt->bind_param(
    'i',
    $id
);

$stmt->execute();

$request =
    $stmt
        ->get_result()
        ->fetch_assoc();

if (!$request) {

    $_SESSION['error'] =
        'Zahtev nije pronađen.';

    header('Location: index.php');
    exit;
}

if ($request['status'] !== 'pending') {

    $_SESSION['error'] =
        'Samo zahtevi na čekanju mogu biti obrađeni.';

    header('Location: index.php');
    exit;
}

$stmt = $conn->prepare("

    UPDATE attendance_absences

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
    'attendance_absences',
    'reject',
    $id,
    'Odbijen zahtev za odsustvo'
);

$_SESSION['success'] =
    'Zahtev odbijen.';

header('Location: index.php');
exit;