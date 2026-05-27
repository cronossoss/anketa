<?php

require_once '../../../config/init.php';

//
// OBRIŠI STARE TEST PODATKE
//

$conn->query("
    DELETE FROM attendance_absences
");

//
// ZAPOSLENI
//

$employees = $conn->query("
    SELECT id
    FROM employees
");

$employeeIds = [];

while ($row = $employees->fetch_assoc()) {

    $employeeIds[] = $row['id'];
}

//
// TIPOVI ODSUSTVA
//

$absenceTypes = [];

$types = $conn->query("
    SELECT id, name
    FROM attendance_absence_types
");

while ($type = $types->fetch_assoc()) {

    $absenceTypes[
        $type['name']
    ] = $type['id'];
}

//
// PERIOD
//

$startDate =
    new DateTime('-30 days');

$endDate =
    new DateTime();

//
// GENERISANJE
//

foreach ($employeeIds as $employeeId) {

    //
    // GODIŠNJI
    //

    if (rand(1, 100) <= 20) {

        $vacationStart =
            clone $startDate;

        $vacationStart->modify(
            '+' . rand(1, 20) . ' days'
        );

        $vacationDays =
            rand(3, 10);

        $vacationEnd =
            clone $vacationStart;

        $vacationEnd->modify(
            '+' . $vacationDays . ' days'
        );

        $stmt = $conn->prepare("
            INSERT INTO attendance_absences (

                employee_id,
                absence_type_id,
                date_from,
                date_to

            )
            VALUES (?, ?, ?, ?)
        ");

        $from =
            $vacationStart->format(
                'Y-m-d 00:00:00'
            );

        $to =
            $vacationEnd->format(
                'Y-m-d 23:59:59'
            );

        $typeId =
            $absenceTypes[
                'Godišnji odmor'
            ];

        $stmt->bind_param(
            'iiss',
            $employeeId,
            $typeId,
            $from,
            $to
        );

        $stmt->execute();
    }

    //
    // LEKAR / IZLAZNICA
    //

    for ($i = 0; $i < rand(1, 3); $i++) {

        $randomDay =
            rand(1, 30);

        $date =
            new DateTime(
                "-$randomDay days"
            );

        $typeName =
            rand(1, 2) === 1
            ? 'Odlazak kod lekara'
            : 'Izlaznica';

        $typeId =
            $absenceTypes[
                $typeName
            ];

        //
        // PRE PODNE ILI POSLE PODNE
        //

        if (rand(1, 2) === 1) {

            $from =
                $date->format(
                    'Y-m-d'
                ) . ' 07:00:00';

            $to =
                $date->format(
                    'Y-m-d'
                ) . ' 09:00:00';

        } else {

            $from =
                $date->format(
                    'Y-m-d'
                ) . ' 13:00:00';

            $to =
                $date->format(
                    'Y-m-d'
                ) . ' 15:00:00';
        }

        $stmt = $conn->prepare("
            INSERT INTO attendance_absences (

                employee_id,
                absence_type_id,
                date_from,
                date_to

            )
            VALUES (?, ?, ?, ?)
        ");

        $stmt->bind_param(
            'iiss',
            $employeeId,
            $typeId,
            $from,
            $to
        );

        $stmt->execute();
    }

    //
    // SLUŽBENI PUT
    //

    if (rand(1, 100) <= 10) {

        $tripDate =
            new DateTime(
                '-' . rand(1, 25) . ' days'
            );

        $tripEnd =
            clone $tripDate;

        $tripEnd->modify(
            '+' . rand(1, 3) . ' days'
        );

        $stmt = $conn->prepare("
            INSERT INTO attendance_absences (

                employee_id,
                absence_type_id,
                date_from,
                date_to

            )
            VALUES (?, ?, ?, ?)
        ");

        $from =
            $tripDate->format(
                'Y-m-d 00:00:00'
            );

        $to =
            $tripEnd->format(
                'Y-m-d 23:59:59'
            );

        $typeId =
            $absenceTypes[
                'Službeni put'
            ];

        $stmt->bind_param(
            'iiss',
            $employeeId,
            $typeId,
            $from,
            $to
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