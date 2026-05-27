<?php

require_once '../../../config/init.php';

require_once '../../../helpers/attendance.php';

//
// OBRIŠI STARI SUMMARY
//

$conn->query("
    DELETE FROM attendance_daily_summary
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
// POSLEDNJIH 30 DANA
//

for ($day = 0; $day < 30; $day++) {

    $date =
        date(
            'Y-m-d',
            strtotime("-$day days")
        );

    //
    // ZAPOSLENI
    //

    foreach ($employeeIds as $employeeId) {

        //
        // SMENA
        //

        $shiftQuery = $conn->prepare("

        SELECT

            s.id,
            s.name,
            s.start_time,
            s.end_time,

            s.allowed_late_minutes,
            s.allowed_early_leave_minutes,

            s.break_minutes,

            s.is_night_shift

        FROM attendance_employee_schedule aes

        JOIN attendance_shifts s
            ON s.id = aes.shift_id

        WHERE aes.employee_id = ?

        AND aes.work_date = ?

        LIMIT 1

    ");

        $shiftQuery->bind_param(
            'is',
            $employeeId,
            $date
        );

        $shiftQuery->execute();

        $shift =
            $shiftQuery
            ->get_result()
            ->fetch_assoc();

        //
        // AKO NEMA SMENE
        //

        if (!$shift) {

            continue;
        }

        //
        // PRVI IN
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
        // POSLEDNJI OUT
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
            $firstInResult['access_datetime']
            ?? null;

        $lastOut =
            $lastOutResult['access_datetime']
            ?? null;

        //
        // ODSUSTVA
        //

        $absenceQuery = $conn->prepare("

            SELECT

                aa.date_from,
                aa.date_to,

                aat.name,
                aat.code,

                aat.category,

                aat.id AS absence_type_id,

                aat.is_paid

            FROM attendance_absences aa

            JOIN attendance_absence_types aat
                ON aat.id = aa.absence_type_id

            WHERE aa.employee_id = ?

            AND DATE(aa.date_from) <= ?
            AND DATE(aa.date_to) >= ?

            AND aa.status = 'approved'

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

        $isJustified = 1;

        //
        // PLAĆENO / NEPLAĆENO
        //

        if ($absence) {

            if ($absence['is_paid']) {

                $paidLeaveMinutes = min(

                    round(

                        (
                            strtotime($absence['date_to'])
                            -
                            strtotime($absence['date_from'])
                        ) / 60

                    ),

                    480

                );
            } else {

                $unpaidLeaveMinutes = min(

                    round(

                        (
                            strtotime($absence['date_to'])
                            -
                            strtotime($absence['date_from'])
                        ) / 60

                    ),

                    480

                );
            }
        }

        //
        // STATUS
        //

        $presenceStatus =
            $firstIn
            ? 'present'
            : 'absent';

        //
        // JUSTIFIED
        //

        $reasonLabel = null;
        $reasonType = null;
        $isJustified = 0;

        if ($absence) {

            $reasonLabel =
                $absence['name'];

            $reasonType =
                $absence['code'];

            $absenceTypeId =
                $absence['absence_type_id']
                ?? null;

            $absenceCategory =
                $absence['category']
                ?? null;

            $isJustified = 1;
        }

        //
        // RADNI MINUTI
        //

        $workedMinutes = 0;

        if ($firstIn && $lastOut) {

            $workedMinutes = round(

                (
                    strtotime($lastOut)
                    -
                    strtotime($firstIn)
                ) / 60
            );
        }

        //
        // OČEKIVANO VREME
        //

        $expectedStart =
            strtotime(
                $date . ' ' .
                    $shift['start_time']
            );

        $expectedEnd =
            strtotime(
                $date . ' ' .
                    $shift['end_time']
            );

        //
        // NOĆNA SMENA
        //

        if (

            strtotime(
                $shift['end_time']
            )

            <=

            strtotime(
                $shift['start_time']
            )

        ) {

            $expectedEnd =
                strtotime(
                    '+1 day',
                    $expectedEnd
                );
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
            // POKRIVA START
            //

            if (

                $absenceFrom <= $expectedStart
                &&
                $absenceTo >= $expectedStart

            ) {

                $expectedStart =
                    $absenceTo;
            }

            //
            // POKRIVA END
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
        // KAŠNJENJE
        //

        $lateMinutes = 0;

        if ($firstIn) {

            $actualStart =
                strtotime($firstIn);

            if (

                $actualStart >

                (
                    $expectedStart
                    +
                    (
                        $shift['allowed_late_minutes'] * 60
                    )
                )

            ) {

                $lateMinutes = round(

                    (
                        $actualStart
                        -
                        $expectedStart
                    ) / 60
                );

                $presenceStatus =
                    'late';
            }
        }

        //
        // RANIJI IZLAZ
        //

        $earlyLeaveMinutes = 0;

        $justifiedEarlyLeaveMinutes = 0;

        $unjustifiedEarlyLeaveMinutes = 0;

        if ($lastOut) {

            $actualEnd =
                strtotime($lastOut);

            if (

                $actualEnd <

                (
                    $expectedEnd
                    -
                    (
                        $shift['allowed_early_leave_minutes'] * 60
                    )
                )

            ) {

                $earlyLeaveMinutes = round(

                    (
                        $expectedEnd
                        -
                        $actualEnd
                    ) / 60
                );

                //
                // OPRAVDAN /
                // NEOPRAVDAN
                //

                if ($reasonLabel) {

                    $justifiedEarlyLeaveMinutes =
                        $earlyLeaveMinutes;
                } else {

                    $unjustifiedEarlyLeaveMinutes =
                        $earlyLeaveMinutes;
                }
            }
        }

        //
        // OVERTIME
        //

        $overtimeMinutes = 0;

        $regularMinutes = 0;

        $nightMinutes = 0;

        $weekendMinutes = 0;

        $mealAllowance = 0;

        $transportAllowance = 0;


        if ($lastOut) {

            $actualEnd =
                strtotime($lastOut);

            if (
                $actualEnd > $expectedEnd
            ) {

                $overtimeMinutes = round(

                    (
                        $actualEnd
                        -
                        $expectedEnd
                    ) / 60
                );
            }
        }

        //
        // REGULAR WORK
        //

        if ($workedMinutes > 0) {

            $regularMinutes = min(

                $workedMinutes,

                (
                    (
                        $expectedEnd
                        -
                        $expectedStart
                    ) / 60
                )

                    -

                    $shift['break_minutes']
            );
        }

        //
        // NIGHT SHIFT
        //

        if (
            $shift['is_night_shift']
        ) {

            $nightMinutes =
                $workedMinutes;
        }

        //
        // WEEKEND
        //

        if (

            date(
                'N',
                strtotime($date)
            ) >= 6

        ) {

            $weekendMinutes =
                $workedMinutes;
        }

        //
        // TOPLI OBROK / PREVOZ
        //

        if ($workedMinutes >= 240) {

            $mealAllowance = 1;

            $transportAllowance = 1;
        }

        //
        // INSERT
        //

        $stmt = $conn->prepare("

            INSERT INTO attendance_daily_summary (

                employee_id,
                work_date,

                first_in,
                last_out,

                worked_minutes,

                late_minutes,
                early_leave_minutes,
                overtime_minutes,

                regular_minutes,
                night_minutes,
                weekend_minutes,

                paid_leave_minutes,
                unpaid_leave_minutes,

                meal_allowance,
                transport_allowance,

                justified_early_leave_minutes,
                unjustified_early_leave_minutes,

                presence_status,

                reason_label,
                reason_type,
                absence_type_id,
                is_justified

            )

            VALUES (

                ?, ?, ?, ?, ?,
                ?, ?, ?,

                ?, ?, ?,

                ?, ?,

                ?, ?,
                ?, ?,


                ?, ?, ?, ?, ?

            )

        ");

        $stmt->bind_param(

            'isssiiiiiiiiiiiiisssii',

            $employeeId,
            $date,

            $firstIn,
            $lastOut,

            $workedMinutes,

            $lateMinutes,
            $earlyLeaveMinutes,
            $overtimeMinutes,

            $regularMinutes,
            $nightMinutes,
            $weekendMinutes,

            $paidLeaveMinutes,
            $unpaidLeaveMinutes,

            $mealAllowance,
            $transportAllowance,

            $justifiedEarlyLeaveMinutes,
            $unjustifiedEarlyLeaveMinutes,

            $presenceStatus,

            $reasonLabel,
            $reasonType,
            $absenceTypeId,
            $isJustified

        );

        $stmt->execute();
    }
}

$_SESSION['success'] =
    'Attendance summary uspešno generisan.';

header(
    'Location: ../dashboard.php'
);

exit;
