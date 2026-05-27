<?php

require_once '../../../config/init.php';

require_once
    '../../../helpers/attendance_balances.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    exit;
}

$userId =
    $_SESSION['user_id'];

//
// EMPLOYEE
//

$employeeQuery = $conn->prepare("

    SELECT employee_id

    FROM users

    WHERE id = ?

    LIMIT 1

");

$employeeQuery->bind_param(
    'i',
    $userId
);

$employeeQuery->execute();

$employeeResult =
    $employeeQuery
    ->get_result()
    ->fetch_assoc();

$employeeId =
    $employeeResult['employee_id']
    ?? null;

if (!$employeeId) {

    die('Korisnik nije povezan sa zaposlenim.');
}

$absenceTypeId =
    (int) $_POST['absence_type_id'];

$dateFrom =
    $_POST['date_from'];

$dateTo =
    $_POST['date_to'];

$balance =

    get_employee_absence_balance(

        $conn,

        $employeeId,

        $absenceTypeId,

        $dateFrom

    );

$requestMinutes = round(

    (
        strtotime($dateTo)
        -
        strtotime($dateFrom)
    ) / 60

);

if (

    $balance['weekly_remaining']
    !== null

    &&

    $requestMinutes
    >
    $balance['weekly_remaining']

) {

    $_SESSION['error'] =

        'Prekoračen nedeljni limit izlaznica.';

    header(
        'Location: ../create.php'
    );

    exit;
}

if (

    $balance['monthly_remaining']
    !== null

    &&

    $requestMinutes
    >
    $balance['monthly_remaining']

) {

    $_SESSION['error'] =

        'Prekoračen mesečni limit izlaznica.';

    header(
        'Location: ../create.php'
    );

    exit;
}

$dateFrom =
    $_POST['date_from'];

$dateTo =
    $_POST['date_to'];

$note =
    trim($_POST['note']);

$stmt = $conn->prepare("

    INSERT INTO attendance_absences (

        employee_id,
        absence_type_id,

        date_from,
        date_to,

        note,

        status,

        requested_by

    )

    VALUES (

        ?, ?, ?, ?,
        ?, 'pending', ?

    )

");

$stmt->bind_param(

    'iisssi',

    $employeeId,
    $absenceTypeId,

    $dateFrom,
    $dateTo,

    $note,

    $employeeId

);

$stmt->execute();

$_SESSION['success'] =
    'Zahtev uspešno poslat.';

header(
    'Location: create.php'
);

exit;
