<?php

require_once '../../../config/init.php';

//
// OBRIŠI STARE LOGOVE
//

$conn->query("
    DELETE FROM attendance_logs
");

//
// ZAPOSLENI
//

$employees = $conn->query("
    SELECT
        id
    FROM employees
");

$employeeIds = [];

while ($row = $employees->fetch_assoc()) {

    $employeeIds[] = $row['id'];
}

//
// POSLEDNJIH 30 DANA
//

for ($day = 0; $day < 30; $day++) {

    $date =
        date(
            'Y-m-d',
            strtotime("-$day days")
        );

    //
    // PRESKOČI VIKEND
    //

    $dayOfWeek =
        date(
            'N',
            strtotime($date)
        );

    if ($dayOfWeek >= 6) {

        continue;
    }

    //
    // ZAPOSLENI
    //

    foreach ($employeeIds as $employeeId) {

        //
        // FULL DAY ODSUSTVO
        //

        $absenceQuery = $conn->prepare("

            SELECT

                aa.date_from,
                aa.date_to,
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

        $absence =
            $absenceQuery
                ->get_result()
                ->fetch_assoc();

        //
        // FULL DAY TYPES
        //

        $fullDayTypes = [

            'Godišnji odmor',
            'Plaćeno odsustvo',
            'Neplaćeno odsustvo',
            'Službeni put'

        ];

        if (
            $absence
            &&
            in_array(
                $absence['name'],
                $fullDayTypes
            )
        ) {

            continue;
        }

        //
        // STANDARDNO VREME
        //

        $startHour = 7;
        $endHour = 15;

        //
        // RANDOM KAŠNJENJE
        //

        $lateMinutes = 0;

        if (rand(1, 100) <= 25) {

            $lateMinutes =
                rand(1, 30);
        }

        //
        // RANDOM RANIJI IZLAZ
        //

        $earlyLeaveMinutes = 0;

        if (rand(1, 100) <= 15) {

            $earlyLeaveMinutes =
                rand(5, 120);
        }

        //
        // PARTIAL ABSENCE
        //

        if ($absence) {

            $absenceFrom =
                strtotime(
                    $absence['date_from']
                );

            $absenceTo =
                strtotime(
                    $absence['date_to']
                );

            //
            // AKO ODSUSTVO POKRIVA
            // POČETAK RADA
            //

            if (
                date('H:i', $absenceFrom)
                <= '07:00'
            ) {

                $startHour =
                    (int) date(
                        'H',
                        $absenceTo
                    );

                $lateMinutes =
                    (int) date(
                        'i',
                        $absenceTo
                    );
            }

            //
            // AKO ODSUSTVO POKRIVA
            // KRAJ RADA
            //

            if (
                date('H:i', $absenceTo)
                >= '15:00'
            ) {

                $endHour =
                    (int) date(
                        'H',
                        $absenceFrom
                    );

                $earlyLeaveMinutes =
                    0;
            }
        }

        //
        // IN
        //

        $inDateTime =
            date(
                'Y-m-d H:i:s',
                strtotime(
                    "$date $startHour:00:00 +$lateMinutes minutes"
                )
            );

        //
        // OUT
        //

        $outDateTime =
            date(
                'Y-m-d H:i:s',
                strtotime(
                    "$date $endHour:00:00 -$earlyLeaveMinutes minutes"
                )
            );

        //
        // POVREMENI OVERTIME
        //

        if (rand(1, 100) <= 10) {

            $outDateTime =
                date(
                    'Y-m-d H:i:s',
                    strtotime(
                        $outDateTime . ' +'
                        . rand(30, 180)
                        . ' minutes'
                    )
                );
        }

        //
        // IN LOG
        //

        $stmt = $conn->prepare("
            INSERT INTO attendance_logs (

                employee_id,
                access_datetime,
                direction

            )
            VALUES (?, ?, 'IN')
        ");

        $stmt->bind_param(
            'is',
            $employeeId,
            $inDateTime
        );

        $stmt->execute();

        //
        // OUT LOG
        //

        $stmt = $conn->prepare("
            INSERT INTO attendance_logs (

                employee_id,
                access_datetime,
                direction

            )
            VALUES (?, ?, 'OUT')
        ");

        $stmt->bind_param(
            'is',
            $employeeId,
            $outDateTime
        );

        $stmt->execute();
    }
}

$_SESSION['success'] =
    'Fake odsustva uspešno generisana.';

header(
    'Location: ../dashboard.php'
);

exit;