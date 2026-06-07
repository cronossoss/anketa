<?php

function getApproverEmployeeId(
    mysqli $conn,
    int $employeeId
): ?int {
    /*
    |--------------------------------------------------------------------------
    | Zaposleni
    |--------------------------------------------------------------------------
    */

    $stmt = $conn->prepare("
        SELECT
            organizational_unit_id
        FROM employees
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->bind_param(
        'i',
        $employeeId
    );

    $stmt->execute();

    $employee =
        $stmt
        ->get_result()
        ->fetch_assoc();

    if (
        !$employee
        || empty($employee['organizational_unit_id'])
    ) {
        return null;
    }

    $unitId =
        (int)$employee['organizational_unit_id'];

    /*
    |--------------------------------------------------------------------------
    | Penjanje kroz hijerarhiju
    |--------------------------------------------------------------------------
    */

    while ($unitId > 0) {

        /*
        |--------------------------------------------------------------------------
        | OJ
        |--------------------------------------------------------------------------
        */

        $stmt = $conn->prepare("
            SELECT
                manager_employee_id
            FROM organizational_units
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->bind_param(
            'i',
            $unitId
        );

        $stmt->execute();

        $unit =
            $stmt
            ->get_result()
            ->fetch_assoc();

        if (!$unit) {
            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | Ako manager nije isti zaposleni
        |--------------------------------------------------------------------------
        */

        if (
            !empty($unit['manager_employee_id'])
            &&
            (int)$unit['manager_employee_id']
            !==
            $employeeId
        ) {

            return (int)$unit['manager_employee_id'];
        }

        /*
        |--------------------------------------------------------------------------
        | Nadređena OJ preko hierarchical relacije
        |--------------------------------------------------------------------------
        */

        $stmt = $conn->prepare("
            SELECT
                parent_id
            FROM organizational_relations
            WHERE child_id = ?
              AND relation_type = 'hierarchical'
            LIMIT 1
        ");

        $stmt->bind_param(
            'i',
            $unitId
        );

        $stmt->execute();

        $relation =
            $stmt
            ->get_result()
            ->fetch_assoc();

        if (!$relation) {
            return null;
        }

        $unitId =
            (int)$relation['parent_id'];
    }

    return null;
}
