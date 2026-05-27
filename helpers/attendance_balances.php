<?php

function get_employee_absence_balance(

    mysqli $conn,

    int $employeeId,

    int $absenceTypeId,

    string $date

): array {

    //
    // TIP ODSUSTVA
    //

    $typeQuery = $conn->prepare("

        SELECT

            weekly_limit_minutes,
            monthly_limit_minutes,
            requires_balance

        FROM attendance_absence_types

        WHERE id = ?

        LIMIT 1

    ");

    $typeQuery->bind_param(
        'i',
        $absenceTypeId
    );

    $typeQuery->execute();

    $type =
        $typeQuery
        ->get_result()
        ->fetch_assoc();

    //
    // NEMA BALANCE
    //

    if (
        !$type
        ||
        !$type['requires_balance']
    ) {

        return [

            'weekly_used' => 0,
            'weekly_remaining' => null,

            'monthly_used' => 0,
            'monthly_remaining' => null

        ];
    }

    //
    // WEEK START
    //

    $weekStart =
        date(
            'Y-m-d',
            strtotime(
                'monday this week',
                strtotime($date)
            )
        );

    //
    // MONTH START
    //

    $monthStart =
        date(
            'Y-m-01',
            strtotime($date)
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

        FROM attendance_absences

        WHERE employee_id = ?

        AND absence_type_id = ?

        AND status = 'approved'

        AND DATE(date_from)
            BETWEEN ? AND ?

    ");

    $weeklyQuery->bind_param(

        'iiss',

        $employeeId,
        $absenceTypeId,

        $weekStart,
        $date

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

        FROM attendance_absences

        WHERE employee_id = ?

        AND absence_type_id = ?

        AND status = 'approved'

        AND DATE(date_from)
            BETWEEN ? AND ?

    ");

    $monthlyQuery->bind_param(

        'iiss',

        $employeeId,
        $absenceTypeId,

        $monthStart,
        $date

    );

    $monthlyQuery->execute();

    $monthlyUsed =

        $monthlyQuery
            ->get_result()
            ->fetch_assoc()['used_minutes']

        ?? 0;

    return [

        'weekly_used' =>
        $weeklyUsed,

        'weekly_remaining' =>

        max(
            0,
            $type['weekly_limit_minutes']
                - $weeklyUsed
        ),

        'monthly_used' =>
        $monthlyUsed,

        'monthly_remaining' =>

        max(
            0,
            $type['monthly_limit_minutes']
                - $monthlyUsed
        )

    ];
}
