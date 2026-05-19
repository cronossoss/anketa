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

    // RADNI MINUTI

    $workedMinutes = 0;

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