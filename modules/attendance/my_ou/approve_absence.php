<?php

require_once '../../../config/init.php';
require_once '../../../helpers/audit.php';

require_login();

$id =
    (int)($_GET['id'] ?? 0);

if (!$id) {

    $_SESSION['error'] =
        'Neispravan zahtev.';

    header(
        'Location: absence_requests.php'
    );

    exit;
}

$userId =
    $_SESSION['user_id'];

//
// EMPLOYEE LOGOVANOG
//

$stmt = $conn->prepare("

    SELECT employee_id

    FROM users

    WHERE id = ?

    LIMIT 1

");

$stmt->bind_param(
    'i',
    $userId
);

$stmt->execute();

$managerEmployeeId =
    $stmt
        ->get_result()
        ->fetch_assoc()['employee_id']
    ?? 0;

//
// OJ KOJOM RUKOVODI
//

$stmt = $conn->prepare("

    SELECT id

    FROM organizational_units

    WHERE manager_employee_id = ?

    LIMIT 1

");

$stmt->bind_param(
    'i',
    $managerEmployeeId
);

$stmt->execute();

$unit =
    $stmt
    ->get_result()
    ->fetch_assoc();

if (!$unit) {

    $_SESSION['error'] =
        'Nemate pravo pristupa.';

    header(
        'Location: absence_requests.php'
    );

    exit;
}

//
// PROVERA ZAHTEVA
//

$stmt = $conn->prepare("

    SELECT

        aa.id,
        aa.status

    FROM attendance_absences aa

    JOIN employees e
        ON e.id = aa.employee_id

    WHERE aa.id = ?

    AND e.organizational_unit_id = ?

    LIMIT 1

");

$stmt->bind_param(
    'ii',
    $id,
    $unit['id']
);

$stmt->execute();

$request =
    $stmt
    ->get_result()
    ->fetch_assoc();

if (!$request) {

    $_SESSION['error'] =
        'Zahtev nije pronađen.';

    header(
        'Location: absence_requests.php'
    );

    exit;
}

if ($request['status'] !== 'pending') {

    $_SESSION['error'] =
        'Zahtev je već obrađen.';

    header(
        'Location: absence_requests.php'
    );

    exit;
}

//
// ODOBRI
//

$stmt = $conn->prepare("

    UPDATE attendance_absences

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

audit_log(
    'attendance_absences',
    'approve',
    $id,
    'Odobreno od strane rukovodioca OJ'
);

$_SESSION['success'] =
    'Zahtev je odobren.';

header(
    'Location: absence_requests.php'
);

exit;
