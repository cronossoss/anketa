<?php

function getChildOrganizationUnits(mysqli $conn, int $parentId): array
{
    $units = [$parentId];

    $stmt = $conn->prepare("
        SELECT id
        FROM organization_units
        WHERE parent_id = ?
          AND active = 1
    ");

    $stmt->bind_param('i', $parentId);
    $stmt->execute();

    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {

        $childId = (int)$row['id'];

        $units = array_merge(
            $units,
            getChildOrganizationUnits($conn, $childId)
        );
    }

    return $units;
}

function getManagedOrganizationUnits(mysqli $conn, int $managerEmployeeId): array
{
    $stmt = $conn->prepare("
        SELECT id
        FROM organization_units
        WHERE manager_employee_id = ?
          AND active = 1
    ");

    $stmt->bind_param('i', $managerEmployeeId);
    $stmt->execute();

    $result = $stmt->get_result();

    $allUnits = [];

    while ($row = $result->fetch_assoc()) {

        $allUnits = array_merge(
            $allUnits,
            getChildOrganizationUnits(
                $conn,
                (int)$row['id']
            )
        );
    }

    return array_unique($allUnits);
}

function getManagerEmployeeId(mysqli $conn, int $userId): ?int
{
    $stmt = $conn->prepare("
        SELECT employee_id
        FROM users
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->bind_param('i', $userId);
    $stmt->execute();

    $result = $stmt->get_result();

    $row = $result->fetch_assoc();

    if (!$row || empty($row['employee_id'])) {
        return null;
    }

    return (int)$row['employee_id'];
}
