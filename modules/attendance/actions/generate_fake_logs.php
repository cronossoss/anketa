<?php

require_once '../../../config/init.php';

require_once '../helpers/fake_data_helper.php';

$date = date('Y-m-d');

$employees = $conn->query("
    SELECT 
        id,
        first_name,
        last_name
    FROM employees
");

if (!$employees) {
    die('Greška pri učitavanju zaposlenih.');
}

while ($employee = $employees->fetch_assoc()) {

    $employeeId = $employee['id'];

    $scenario = generateFakeScenario();

    // Odsutan radnik
    if ($scenario === 'absent') {
        continue;
    }

    $inTime = '07:00:00';
    $outTime = '15:00:00';

    switch ($scenario) {

        case 'normal':

            $inTime = generateRandomTime(
                '07:00:00',
                5
            );

            // 50% radnika još nije izašlo

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

            $outTime = generateRandomTime(
                '15:00:00',
                5
            );

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

            $outTime = generateRandomTime(
                '17:00:00',
                20
            );

            break;
    }

    // IN log

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

    // OUT log

    if ($outTime) {

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

echo "Fake logovi uspešno generisani.";