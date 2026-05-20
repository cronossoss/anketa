<?php

require_once '../../../config/init.php';

$date = date('Y-m-d');

$employees = $conn->query("
    SELECT 
        id,
        first_name,
        last_name
    FROM employees
");

while ($employee = $employees->fetch_assoc()) {

    $employeeId = $employee['id'];

    // PRVI IN

    $firstInQuery = $conn->prepare("
        SELECT access_datetime
        FROM attendance_logs
        WHERE employee_id = ?
        AND direction = 'IN'
        AND DATE(access_datetime) = ?
        ORDER BY access_datetime ASC
        LIMIT 1
    ");

    $firstInQuery->bind_param(
        'is',
        $employeeId,
        $date
    );

    $firstInQuery->execute();

    $firstInResult =
        $firstInQuery
            ->get_result()
            ->fetch_assoc();

    // POSLEDNJI OUT

    $lastOutQuery = $conn->prepare("
        SELECT access_datetime
        FROM attendance_logs
        WHERE employee_id = ?
        AND direction = 'OUT'
        AND DATE(access_datetime) = ?
        ORDER BY access_datetime DESC
        LIMIT 1
    ");

    $lastOutQuery->bind_param(
        'is',
        $employeeId,
        $date
    );

    $lastOutQuery->execute();

    $lastOutResult =
        $lastOutQuery
            ->get_result()
            ->fetch_assoc();

    $firstIn =
        $firstInResult['access_datetime'] ?? null;

    $lastOut =
        $lastOutResult['access_datetime'] ?? null;

    // STATUS

    $presenceStatus =
        $firstIn ? 'present' : 'absent';

//
// ODSUSTVA
//

$absenceQuery = $conn->prepare("

    SELECT

        aa.date_from,
        aa.date_to,
        aat.code

    FROM attendance_absences aa

    JOIN attendance_absence_types aat
        ON aat.id = aa.absence_type_id

    WHERE aa.employee_id = ?

    AND DATE(aa.date_from) <= ?
    AND DATE(aa.date_to) >= ?

    LIMIT 1

");

$absenceQuery->bind_param(
    'iss',
    $employeeId,
    $date,
    $date
);

$absenceQuery->execute();

$absenceResult =
    $absenceQuery
        ->get_result()
        ->fetch_assoc();

//
// CELODNEVNO ODSUSTVO
//

if (
    $absenceResult
    &&
    !$firstIn
) {

    $presenceStatus =
        $absenceResult['code'];
}

    // RADNI MINUTI

    $workedMinutes = 0;

    $lateMinutes = 0;

        if ($firstIn) {

    $expectedStart =
        strtotime($date . ' 07:00:00');

    //
    // AKO POSTOJI ODSUSTVO
    //

    if ($absenceResult) {

        $absenceFrom =
            strtotime(
                $absenceResult['date_from']
            );

        $absenceTo =
            strtotime(
                $absenceResult['date_to']
            );

        //
        // AKO ODSUSTVO POKRIVA
        // POČETAK RADNOG VREMENA
        //

        if (
            $absenceFrom <= $expectedStart
            &&
            $absenceTo >= $expectedStart
        ) {

            $expectedStart =
                $absenceTo;
        }
    }

    $actualStart =
        strtotime($firstIn);

    if ($actualStart > $expectedStart) {

        $lateMinutes = round(
            ($actualStart - $expectedStart) / 60
        );

        $presenceStatus = 'late';
    }
}

    if ($firstIn && $lastOut) {

        $workedMinutes =
            round(
                (
                    strtotime($lastOut)
                    - strtotime($firstIn)
                ) / 60
            );
    }

    // INSERT / UPDATE

    $stmt = $conn->prepare("
        INSERT INTO attendance_daily_summary (

            employee_id,
            work_date,
            first_in,
            last_out,
            worked_minutes,
            late_minutes,
            presence_status

        )
        VALUES (?, ?, ?, ?, ?, ?, ?)

        ON DUPLICATE KEY UPDATE

            first_in = VALUES(first_in),
            last_out = VALUES(last_out),
            worked_minutes = VALUES(worked_minutes),
            late_minutes = VALUES(late_minutes),
            presence_status = VALUES(presence_status)
    ");

    $stmt->bind_param(
        'isssiis',
        $employeeId,
        $date,
        $firstIn,
        $lastOut,
        $workedMinutes,
        $lateMinutes,
        $presenceStatus
    );

    $stmt->execute();
}

echo "Daily summary uspešno generisan.";