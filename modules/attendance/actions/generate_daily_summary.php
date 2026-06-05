<?php

require_once '../../../config/init.php';

require_once '../../../helpers/attendance.php';

$date = $_GET['date']
    ?? date('Y-m-d');

if (
    !preg_match(
        '/^\d{4}-\d{2}-\d{2}$/',
        $date
    )
) {
    die('Neispravan datum.');
}

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

        aat.code,
        aat.name

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
    
    
$exitPassQuery = $conn->prepare("

    SELECT

        date_from,
        date_to

    FROM attendance_exit_passes

    WHERE employee_id = ?

    AND status = 'approved'

    AND DATE(date_from) = ?

");

$exitPassQuery->bind_param(
    'is',
    $employeeId,
    $date
);

$exitPassQuery->execute();

$exitPassResult =
    $exitPassQuery
        ->get_result();

$exitPasses = [];

while ($row = $exitPassResult->fetch_assoc()) {

    $exitPasses[] = $row;
}

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

    $reasonLabel = null;

    $reasonType = null;

    $isJustified = 0;

    $earlyLeaveMinutes = 0;

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

            $reasonLabel =
                $absenceResult['name'];

            $reasonType =
                $absenceResult['code'];

            $isJustified = 1;

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

    $workedMinutes = 0;

$logsQuery = $conn->prepare("

    SELECT

        access_datetime,
        direction

    FROM attendance_logs

    WHERE employee_id = ?

    AND DATE(access_datetime) = ?

    ORDER BY access_datetime ASC

");

$logsQuery->bind_param(
    'is',
    $employeeId,
    $date
);

$logsQuery->execute();

$logs =
    $logsQuery
        ->get_result();

$lastIn = null;

while ($log = $logs->fetch_assoc()) {

    if ($log['direction'] === 'IN') {

        $lastIn =
            strtotime(
                $log['access_datetime']
            );

    } elseif (

        $log['direction'] === 'OUT'
        &&
        $lastIn

    ) {

        $workedMinutes += round(

            (
                strtotime(
                    $log['access_datetime']
                )
                -
                $lastIn
            ) / 60

        );

        $lastIn = null;
    }
}

    //
    // RANIJI IZLAZ
    //

    if ($lastOut) {

        //
        // PODRAZUMEVANI KRAJ
        //

        $expectedEnd =
            strtotime($date . ' 15:00:00');

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
            // KRAJ RADNOG VREMENA
            //

            if (
                $absenceFrom <= $expectedEnd
                &&
                $absenceTo >= $expectedEnd
            ) {

                $expectedEnd =
                    $absenceFrom;
            }
        }

        //
        // REALAN IZLAZ
        //

        $actualEnd =
            strtotime($lastOut);

        //
        // RANIJI IZLAZ
        //

        foreach ($exitPasses as $exitPass) {

    $passFrom =
        strtotime($exitPass['date_from']);

    $passTo =
        strtotime($exitPass['date_to']);

    if (

        $passFrom <= $expectedEnd

        &&

        $passTo >= $expectedEnd

    ) {

        $expectedEnd =
            $passFrom;

        $reasonLabel =
            'Odobrena izlaznica';

        $reasonType =
            'EXIT_PASS';

        $isJustified = 1;
    }
}

if ($reasonType === 'EXIT_PASS') {

    $presenceStatus =
        'approved_exit';
}


        if ($actualEnd < $expectedEnd) {

    $justifiedExit = false;

    foreach ($exitPasses as $exitPass) {

        $passFrom =
            strtotime(
                $exitPass['date_from']
            );

        $passTo =
            strtotime(
                $exitPass['date_to']
            );

        if (

            $passFrom <= $expectedEnd

            &&

            $passTo >= $expectedEnd

        ) {

            $justifiedExit = true;

            $reasonLabel =
    'Odobrena izlaznica';

$reasonType =
    'EXIT_PASS';

$isJustified = 1;


        }
    }

    if (!$justifiedExit) {

        $earlyLeaveMinutes = round(
            ($expectedEnd - $actualEnd) / 60
        );
    }
}
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
            early_leave_minutes,
            reason_label,
            reason_type,
            is_justified,
            presence_status

        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)

        ON DUPLICATE KEY UPDATE

            first_in = VALUES(first_in),
            last_out = VALUES(last_out),
            worked_minutes = VALUES(worked_minutes),
            late_minutes = VALUES(late_minutes),
            early_leave_minutes = VALUES(early_leave_minutes),
            reason_label = VALUES(reason_label),
            reason_type = VALUES(reason_type),
            is_justified = VALUES(is_justified),
            presence_status = VALUES(presence_status)
    ");

    $stmt->bind_param(
        'isssiiissis',
        $employeeId,
        $date,
        $firstIn,
        $lastOut,
        $workedMinutes,
        $lateMinutes,
        $earlyLeaveMinutes,
        $reasonLabel,
        $reasonType,
        $isJustified,
        $presenceStatus
    );

    $stmt->execute();
}

$_SESSION['success'] =
    'Attendance summary uspešno generisan.';

header(
    'Location: ../dashboard.php'
);

exit;