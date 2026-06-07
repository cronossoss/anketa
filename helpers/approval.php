<?php

function getEmployeeApprovalLevel(
    mysqli $conn,
    int $employeeId
): int {

    $stmt = $conn->prepare("
        SELECT approval_level
        FROM employees
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->bind_param(
        'i',
        $employeeId
    );

    $stmt->execute();

    $row = $stmt
        ->get_result()
        ->fetch_assoc();

    return (int)($row['approval_level'] ?? 1);
}
