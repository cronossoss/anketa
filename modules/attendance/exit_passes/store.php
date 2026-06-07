<?php

require_once '../../../config/init.php';

require_login();

$employeeId =
    (int)$_POST['employee_id'];

$exitTypeId =
    (int)$_POST['exit_type_id'];

$dateFrom =
    $_POST['date_from'];

$dateTo =
    $_POST['date_to'];

$note =
    trim(
        $_POST['note'] ?? ''
    );

//
// TIP IZLASKA
//

$typeQuery = $conn->prepare("

    SELECT

        weekly_limit_minutes,
        monthly_limit_minutes,
        requires_balance

    FROM attendance_exit_types

    WHERE id = ?

    LIMIT 1

");

$typeQuery->bind_param(
    'i',
    $exitTypeId
);

$typeQuery->execute();

$exitType =
    $typeQuery
    ->get_result()
    ->fetch_assoc();

//
// VALIDACIJA
//

if (

    strtotime($dateTo)

    <=

    strtotime($dateFrom)

) {

    $_SESSION['error'] =
        'Datum završetka mora biti veći od datuma početka.';

    header(
        'Location: create.php'
    );

    exit;
}

//
// PROVERA PREKLAPANJA
//

$overlapQuery = $conn->prepare("

    SELECT id

    FROM attendance_exit_passes

    WHERE employee_id = ?

    AND status IN ('pending','approved')

    AND (

           (? BETWEEN date_from AND date_to)

        OR (? BETWEEN date_from AND date_to)

        OR (date_from BETWEEN ? AND ?)

    )

    LIMIT 1

");

$overlapQuery->bind_param(

    'issss',

    $employeeId,

    $dateFrom,
    $dateTo,

    $dateFrom,
    $dateTo

);

$overlapQuery->execute();

if (

    $overlapQuery
    ->get_result()
    ->num_rows > 0

) {

    $_SESSION['error'] =
        'Za ovaj period već postoji izlaznica.';

    header(
        'Location: create.php'
    );

    exit;
}

//
// TRAJANJE
//

$requestMinutes = round(

    (
        strtotime($dateTo)
        -
        strtotime($dateFrom)
    ) / 60

);

//
// IZLAZNICA
// (tip 1)
//

if (

    $exitType
    &&
    $exitType['requires_balance']

) {

    //
    // POČETAK NEDELJE
    //

    $weekStart = date(
        'Y-m-d',
        strtotime(
            'monday this week',
            strtotime($dateFrom)
        )
    );

    //
    // POČETAK MESECA
    //

    $monthStart = date(
        'Y-m-01',
        strtotime($dateFrom)
    );

    //
    // WEEKLY USED
    //

    $weeklyQuery = $conn->prepare("

        SELECT

            SUM(

                TIMESTAMPDIFF(

                    MINUTE,

                    date_from,
                    date_to

                )

            ) AS used_minutes

        FROM attendance_exit_passes

        WHERE employee_id = ?

        AND exit_type_id = ?

        AND status = 'approved'

        AND DATE(date_from)
            BETWEEN ? AND ?

    ");

    $currentDate =
        date(
            'Y-m-d',
            strtotime($dateFrom)
        );

    $weeklyQuery->bind_param(

        'iiss',

        $employeeId,
        $exitTypeId,
        $weekStart,
        $currentDate

    );

    $weeklyQuery->execute();

    $weeklyUsed =

        $weeklyQuery
            ->get_result()
            ->fetch_assoc()['used_minutes']

        ?? 0;

    //
    // MONTHLY USED
    //

    $monthlyQuery = $conn->prepare("

        SELECT

            SUM(

                TIMESTAMPDIFF(

                    MINUTE,

                    date_from,
                    date_to

                )

            ) AS used_minutes

        FROM attendance_exit_passes

        WHERE employee_id = ?

        AND exit_type_id = ?

        AND status = 'approved'

        AND DATE(date_from)
            BETWEEN ? AND ?

    ");

    $monthlyQuery->bind_param(

        'iiss',

        $employeeId,
        $exitTypeId,
        $monthStart,
        $currentDate

    );

    $monthlyQuery->execute();

    $monthlyUsed =

        $monthlyQuery
            ->get_result()
            ->fetch_assoc()['used_minutes']

        ?? 0;

    //
    // LIMITI
    //

    if (

        ($weeklyUsed + $requestMinutes)

        >

        (int)$exitType['weekly_limit_minutes']

    ) {

        $_SESSION['error'] =

            'Prekoračen nedeljni limit izlaznica (4h).';

        header(
            'Location: create.php'
        );

        exit;
    }

    if (

        ($monthlyUsed + $requestMinutes)

        >

        (int)$exitType['monthly_limit_minutes']

    ) {

        $_SESSION['error'] =

            'Prekoračen mesečni limit izlaznica (8h).';

        header(
            'Location: create.php'
        );

        exit;
    }
}

//
// INSERT
//

$status = 'approved';

$source = 'manager_created';

$requestedBy =
    $_SESSION['user_id']
    ?? null;

$approvedBy =
    $_SESSION['user_id']
    ?? null;

$stmt = $conn->prepare("

    INSERT INTO attendance_exit_passes (

        employee_id,

        exit_type_id,

        date_from,
        date_to,

        note,

        status,

        source,

        requested_by,

        approved_by,

        approved_at

    )

    VALUES (

        ?, ?,

        ?, ?,

        ?,

        ?,

        ?,

        ?,

        ?,

        NOW()

    )

");

$stmt->bind_param(

    'iisssssii',

    $employeeId,

    $exitTypeId,

    $dateFrom,
    $dateTo,

    $note,

    $status,

    $source,

    $requestedBy,

    $approvedBy

);

if (!$stmt) {

    die('Prepare error: '
        . $conn->error);
}

if (!$stmt->execute()) {

    die('Execute error: '
        . $stmt->error);
}

$_SESSION['success'] =
    'Izlaznica uspešno sačuvana.';

header(
    'Location: index.php'
);

exit;
