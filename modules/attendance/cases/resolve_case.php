<?php

require_once '../../../config/init.php';
require_once '../../../helpers/audit.php';

require_login();
require_role([
    'admin',
    'hr',
    'manager'
]);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit;
}

if (!verify_csrf($_POST['csrf'] ?? '')) {
    exit('CSRF');
}

$caseId =
    (int)($_POST['case_id'] ?? 0);

$resolutionType =
    trim($_POST['resolution_type'] ?? '');

$resolutionNote =
    trim($_POST['resolution_note'] ?? '');

$dateFrom =
    trim($_POST['date_from'] ?? '');

$timeFrom =
    trim($_POST['time_from'] ?? '');

$dateTo =
    trim($_POST['date_to'] ?? '');

$timeTo =
    trim($_POST['time_to'] ?? '');

if (
    !$caseId ||
    !$resolutionType ||
    !$dateFrom ||
    !$dateTo
) {
    exit('Nedostaju podaci');
}

// UČITAVANJE SLUČAJA

$stmt = $conn->prepare("
    SELECT *
    FROM attendance_cases
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param(
    'i',
    $caseId
);

$stmt->execute();

$case =
    $stmt
        ->get_result()
        ->fetch_assoc();

if (!$case) {
    exit('Slučaj nije pronađen.');
}

$employeeId =
    (int)$case['employee_id'];

$userId =
    (int)$_SESSION['user_id'];

$managerEmployeeId =
    (int)$_SESSION['employee_id'];

// DATUMI

$dateTimeFrom =
    $dateFrom .
    ' ' .
    ($timeFrom ?: '00:00');

$dateTimeTo =
    $dateTo .
    ' ' .
    ($timeTo ?: '23:59');

// TRANSAKCIJA

$conn->begin_transaction();

try {

    $linkedAbsenceId = null;
    $linkedExitPassId = null;
    $linkedAttendanceLogId = null;

// PRISUTAN

if ($resolutionType === 'present') {

    $stmt = $conn->prepare("
        INSERT INTO attendance_logs
        (
            employee_id,
            access_datetime,
            direction,
            terminal_name,
            source_system,
            is_simulated
        )
        VALUES
        (
            ?,
            ?,
            'IN',
            'Case resolution',
            'case_resolution',
            1
        )
    ");

    $stmt->bind_param(
        'is',
        $employeeId,
        $dateTimeFrom
    );

    $stmt->execute();

    $linkedAttendanceLogId =
        $conn->insert_id;
}

// IZLAZNICE

$exitTypeMap = [

    'doctor' => 2,
    'private_exit' => 3,
    'official_exit' => 4
];

if (
    isset(
        $exitTypeMap[
            $resolutionType
        ]
    )
) {

    $exitTypeId =
        $exitTypeMap[
            $resolutionType
        ];

    $stmt = $conn->prepare("
        INSERT INTO attendance_exit_passes
        (
            employee_id,
            exit_type_id,
            date_from,
            date_to,
            note,
            status,
            source,
            requested_by,
            approved_by_employee_id,
            approved_by,
            approved_at
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            ?,
            'approved',
            'manager_created',
            NULL,
            ?,
            ?,
            NOW()
        )
    ");

    $stmt->bind_param(
        'iisssii',
        $employeeId,
        $exitTypeId,
        $dateTimeFrom,
        $dateTimeTo,
        $resolutionNote,
        $managerEmployeeId,
        $userId
    );

    $stmt->execute();

    $linkedExitPassId =
        $conn->insert_id;
}

// ODSUSTVA

$absenceTypeMap = [

    'sick_leave' => 2,
    'vacation' => 8,
    'unpaid_leave' => 10,
    'business_trip' => 12,
    'unexcused' => 9
];

if (
    isset(
        $absenceTypeMap[
            $resolutionType
        ]
    )
) {

    $absenceTypeId =
        $absenceTypeMap[
            $resolutionType
        ];

    $stmt = $conn->prepare("
        INSERT INTO attendance_absences
        (
            employee_id,
            absence_type_id,
            date_from,
            date_to,
            note,
            status,
            source,
            resolution_source,
            created_by_user_id,
            approved_by_employee_id,
            approved_at
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            ?,
            'approved',
            'manager_created',
            'case_resolution',
            ?,
            ?,
            NOW()
        )
    ");

    $stmt->bind_param(
        'iisssii',
        $employeeId,
        $absenceTypeId,
        $dateTimeFrom,
        $dateTimeTo,
        $resolutionNote,
        $userId,
        $managerEmployeeId
    );

    $stmt->execute();

    $linkedAbsenceId =
        $conn->insert_id;
}

// ZATVARANJE SLUČAJA

$stmt = $conn->prepare("
    UPDATE attendance_cases
    SET
        case_status = 'closed',
        resolution_type = ?,
        resolution_note = ?,
        resolution_date_from = ?,
        resolution_date_to = ?,
        linked_absence_id = ?,
        linked_exit_pass_id = ?,
        linked_attendance_log_id = ?,
        closed_by_user_id = ?,
        closed_at = NOW()
    WHERE id = ?
");

$stmt->bind_param(
    'ssssiiiii',
    $resolutionType,
    $resolutionNote,
    $dateTimeFrom,
    $dateTimeTo,
    $linkedAbsenceId,
    $linkedExitPassId,
    $linkedAttendanceLogId,
    $userId,
    $caseId
);

$stmt->execute();

// AUDIT + KRAJ

audit_log(
    'attendance_case',
    'close',
    $caseId,
    $resolutionType
);

$conn->commit();

$_SESSION['success'] =
    'Slučaj je uspešno zatvoren.';

}
catch (Throwable $e) {

    $conn->rollback();

    $_SESSION['error'] =
        $e->getMessage();
}

redirect(
    'modules/attendance/cases/index.php'
);

