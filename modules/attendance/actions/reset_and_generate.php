<?php

require_once '../../../config/init.php';

require_once '../helpers/fake_data_helper.php';

$date = date('Y-m-d');

//
// 1. OBRIŠI FAKE LOGOVE
//

$conn->query("
    DELETE FROM attendance_logs
    WHERE is_simulated = 1
");

//
// 2. OBRIŠI SUMMARY
//

$conn->query("
    DELETE FROM attendance_daily_summary
");

//
// 3. GENERIŠI FAKE LOGOVE
//

$employees = $conn->query("
    SELECT 
        id,
        first_name,
        last_name
    FROM employees
");

while ($employee = $employees->fetch_assoc()) {

    $employeeId = $employee['id'];

    $scenario = generateFakeScenario();

    if ($scenario === 'absent') {
        continue;
    }

    $inTime = '07:00:00';

    switch ($scenario) {

        case 'normal':

            $inTime = generateRandomTime(
                '07:00:00',
                5
            );

            // 80% ostaje unutra

            if (rand(1, 100) <= 80) {

                $outTime = null;

            } else {

                $outTime = generateRandomTime(
                    '15:00:00',
                    5
                );
            }

            break;

        case 'late':

            $inTime = generateRandomTime(
                '07:20:00',
                10
            );

            $outTime = null;

            break;

        case 'early_leave':

            $inTime = generateRandomTime(
                '07:00:00',
                5
            );

            $outTime = generateRandomTime(
                '14:30:00',
                10
            );

            break;

        case 'missing_out':

            $inTime = generateRandomTime(
                '07:00:00',
                5
            );

            $outTime = null;

            break;

        case 'overtime':

            $inTime = generateRandomTime(
                '07:00:00',
                5
            );

            $outTime = null;

            break;
    }

    //
    // IN
    //

    $inDateTime = $date . ' ' . $inTime;

    $stmt = $conn->prepare("
        INSERT INTO attendance_logs (
            employee_id,
            access_datetime,
            direction,
            terminal_name,
            source_system,
            is_simulated
        )
        VALUES (?, ?, 'IN', 'SIMULATOR', 'simulator', 1)
    ");

    $stmt->bind_param(
        'is',
        $employeeId,
        $inDateTime
    );

    $stmt->execute();

    //
    // OUT
    //

    if (!empty($outTime)) {

        $outDateTime = $date . ' ' . $outTime;

        $stmt = $conn->prepare("
            INSERT INTO attendance_logs (
                employee_id,
                access_datetime,
                direction,
                terminal_name,
                source_system,
                is_simulated
            )
            VALUES (?, ?, 'OUT', 'SIMULATOR', 'simulator', 1)
        ");

        $stmt->bind_param(
            'is',
            $employeeId,
            $outDateTime
        );

        $stmt->execute();
    }
}

//
// 4. GENERIŠI SUMMARY
//

$employees = $conn->query("
    SELECT id
    FROM employees
");

while ($employee = $employees->fetch_assoc()) {

    $employeeId = $employee['id'];

    //
    // FIRST IN
    //

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

    //
    // LAST OUT
    //

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

    //
    // STATUS
    //

    $presenceStatus =
        $firstIn ? 'present' : 'absent';

    //
    // WORKED MINUTES
    //

    $workedMinutes = 0;

    if ($firstIn && $lastOut) {

        $workedMinutes = round(
            (
                strtotime($lastOut)
                - strtotime($firstIn)
            ) / 60
        );
    }

    //
    // LATE MINUTES
    //

    $lateMinutes = 0;

    if ($firstIn) {

        $expectedStart =
            strtotime($date . ' 07:00:00');

        $actualStart =
            strtotime($firstIn);

        if ($actualStart > $expectedStart) {

            $lateMinutes = round(
                ($actualStart - $expectedStart) / 60
            );
        }
    }

    //
    // INSERT SUMMARY
    //

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

header('Location: ../dashboard.php');

exit;