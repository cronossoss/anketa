<?php

/**
 * Izračunava sve metrike za jednog zaposlenog za jedan dan
 * 
 * @param mysqli $conn Database connection
 * @param int $employeeId
 * @param string $date (Y-m-d format)
 * @return array
 */
function calculate_daily_attendance($conn, $employeeId, $date)
{
    // Podrazumevane vrednosti (default raspored 07:00-15:00)
    $expectedStart = strtotime($date . ' 07:00:00');
    $expectedEnd = strtotime($date . ' 15:00:00');

    // 1. Učitaj dodeljeni raspored za tog zaposlenog za taj dan
    $scheduleQuery = $conn->prepare("
        SELECT 
            aws.expected_start,
            aws.expected_end,
            aws.late_tolerance_minutes
        FROM attendance_schedule_assignments asa
        JOIN attendance_work_schedules aws ON aws.id = asa.schedule_id
        WHERE asa.employee_id = ?
        AND asa.valid_from <= ?
        ORDER BY asa.valid_from DESC
        LIMIT 1
    ");

    $scheduleQuery->bind_param('is', $employeeId, $date);
    $scheduleQuery->execute();
    $schedule = $scheduleQuery->get_result()->fetch_assoc();

    if ($schedule) {
        $expectedStart = strtotime($date . ' ' . $schedule['expected_start']);
        $expectedEnd = strtotime($date . ' ' . $schedule['expected_end']);
        $lateTolerance = (int)($schedule['late_tolerance_minutes'] ?? 0);
    } else {
        $lateTolerance = 5; // default tolerance
    }

    // 2. Učitaj logove za taj dan
    $logsQuery = $conn->prepare("
        SELECT access_datetime, direction
        FROM attendance_logs
        WHERE employee_id = ? AND DATE(access_datetime) = ?
        ORDER BY access_datetime ASC
    ");
    $logsQuery->bind_param('is', $employeeId, $date);
    $logsQuery->execute();
    $logs = $logsQuery->get_result();

    $firstIn = null;
    $lastOut = null;
    $lastIn = null;
    $workedMinutes = 0;

    while ($log = $logs->fetch_assoc()) {
        $timestamp = strtotime($log['access_datetime']);

        if ($log['direction'] === 'IN') {
            if ($firstIn === null) {
                $firstIn = $log['access_datetime'];
            }
            $lastIn = $timestamp;
        } elseif ($log['direction'] === 'OUT' && $lastIn !== null) {
            $workedMinutes += round(($timestamp - $lastIn) / 60);
            $lastOut = $log['access_datetime'];
            $lastIn = null;
        }
    }

    // 3. Učitaj odsustva za taj dan
    $absenceQuery = $conn->prepare("
        SELECT 
            aa.date_from, aa.date_to,
            aat.name, aat.code, aat.id AS absence_type_id,
            aat.is_paid
        FROM attendance_absences aa
        JOIN attendance_absence_types aat ON aat.id = aa.absence_type_id
        WHERE aa.employee_id = ? 
        AND aa.status = 'approved'
        AND DATE(aa.date_from) <= ? AND DATE(aa.date_to) >= ?
        LIMIT 1
    ");
    $absenceQuery->bind_param('iss', $employeeId, $date, $date);
    $absenceQuery->execute();
    $absence = $absenceQuery->get_result()->fetch_assoc();

    // 4. Učitaj izlaznice za taj dan
    $exitQuery = $conn->prepare("
        SELECT date_from, date_to
        FROM attendance_exit_passes
        WHERE employee_id = ? AND status = 'approved'
        AND DATE(date_from) = ?
    ");
    $exitQuery->bind_param('is', $employeeId, $date);
    $exitQuery->execute();
    $exitPasses = $exitQuery->get_result()->fetch_all(MYSQLI_ASSOC);

    // 5. Izračunaj late minutes
    $lateMinutes = 0;
    $presenceStatus = $firstIn ? 'present' : 'absent';

    if ($firstIn) {
        $actualStart = strtotime($firstIn);
        $allowedStart = $expectedStart + ($lateTolerance * 60);

        if ($actualStart > $allowedStart) {
            $lateMinutes = round(($actualStart - $expectedStart) / 60);
            $presenceStatus = 'late';
        }
    }

    // 6. Izračunaj early leave minutes
    $earlyLeaveMinutes = 0;
    $isJustified = 0;
    $reasonLabel = null;
    $reasonType = null;
    $absenceTypeId = null;

    if ($absence) {
        $reasonLabel = $absence['name'];
        $reasonType = $absence['code'];
        $absenceTypeId = $absence['absence_type_id'];
        $isJustified = 1;
        $presenceStatus = $absence['code'];
    }

    if ($lastOut) {
        $actualEnd = strtotime($lastOut);
        $justifiedExit = false;

        foreach ($exitPasses as $exit) {
            $exitFrom = strtotime($exit['date_from']);
            $exitTo = strtotime($exit['date_to']);
            if ($exitFrom <= $actualEnd && $exitTo >= $actualEnd) {
                $justifiedExit = true;
                $reasonLabel = 'Odobrena izlaznica';
                $reasonType = 'EXIT_PASS';
                $isJustified = 1;
                $presenceStatus = 'approved_exit';
                break;
            }
        }

        if (!$justifiedExit && $actualEnd < $expectedEnd) {
            $earlyLeaveMinutes = round(($expectedEnd - $actualEnd) / 60);
        }
    }

    // 7. Overtime
    $overtimeMinutes = 0;
    if ($lastOut) {
        $actualEnd = strtotime($lastOut);
        if ($actualEnd > $expectedEnd) {
            $overtimeMinutes = round(($actualEnd - $expectedEnd) / 60);
        }
    }

    return [
        'first_in' => $firstIn,
        'last_out' => $lastOut,
        'worked_minutes' => $workedMinutes,
        'late_minutes' => $lateMinutes,
        'early_leave_minutes' => $earlyLeaveMinutes,
        'overtime_minutes' => $overtimeMinutes,
        'presence_status' => $presenceStatus,
        'reason_label' => $reasonLabel,
        'reason_type' => $reasonType,
        'absence_type_id' => $absenceTypeId,
        'is_justified' => $isJustified
    ];
}
