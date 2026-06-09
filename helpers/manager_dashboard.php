<?php

require_once __DIR__ . '/organization_tree.php';

function getManagedEmployeeIds(
    mysqli $conn,
    int $managerEmployeeId
): array {

    $units =
        getManagedOrganizationUnits(
            $conn,
            $managerEmployeeId
        );

    if (empty($units)) {
        return [];
    }

    $unitIds =
        implode(
            ',',
            array_map(
                'intval',
                $units
            )
        );

    $employees = [];

    $result = $conn->query("
        SELECT id
        FROM employees
        WHERE organizational_unit_id IN ($unitIds)
    ");

    while ($row = $result->fetch_assoc()) {

        $employees[] =
            (int)$row['id'];
    }

    return $employees;
}

function getManagedEmployeeCount(
    mysqli $conn,
    int $managerEmployeeId
): int {

    return count(
        getManagedEmployeeIds(
            $conn,
            $managerEmployeeId
        )
    );
}

function getPendingAbsenceCount(
    mysqli $conn,
    int $managerEmployeeId
): int {

    $employees =
        getManagedEmployeeIds(
            $conn,
            $managerEmployeeId
        );

    if (empty($employees)) {
        return 0;
    }

    $employeeIds =
        implode(
            ',',
            array_map(
                'intval',
                $employees
            )
        );

    $result = $conn->query("
        SELECT COUNT(*) total
        FROM attendance_absences
        WHERE employee_id IN ($employeeIds)
          AND status = 'pending'
    ");

    return (int)
    $result
        ->fetch_assoc()['total'];
}

function getPendingExitPassCount(
    mysqli $conn,
    int $managerEmployeeId
): int {

    $employees =
        getManagedEmployeeIds(
            $conn,
            $managerEmployeeId
        );

    if (empty($employees)) {
        return 0;
    }

    $employeeIds =
        implode(
            ',',
            array_map(
                'intval',
                $employees
            )
        );

    $result = $conn->query("
        SELECT COUNT(*) total
        FROM attendance_exit_passes
        WHERE employee_id IN ($employeeIds)
          AND status = 'pending'
    ");

    return (int)
    $result
        ->fetch_assoc()['total'];
}
