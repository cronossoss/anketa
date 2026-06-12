<?php

require_once '../../../config/init.php';
require_once '../../../helpers/audit.php';

require_login();
require_role(['admin', 'hr', 'manager']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit;
}

if (!verify_csrf($_POST['csrf'] ?? '')) {
    exit('CSRF');
}

$employeeId =
    (int)($_POST['employee_id'] ?? 0);

$caseType =
    trim($_POST['case_type'] ?? '');

$description =
    trim($_POST['description'] ?? '');

if (
    !$employeeId ||
    empty($caseType) ||
    empty($description)
) {
    exit('Nedostaju podaci');
}

$userId =
    (int)$_SESSION['user_id'];

$titleMap = [

    'missing_attendance' =>
        'Neevidentiran dolazak',

    'doctor' =>
        'Odlazak kod lekara',

    'business_trip' =>
        'Službeni put',

    'private_exit' =>
        'Privatni izlazak',

    'official_exit' =>
        'Službeni izlazak',

    'tardiness' =>
        'Kašnjenje'
];

$title =
    $titleMap[$caseType]
    ?? 'Slučaj evidencije';

$stmt = $conn->prepare("
    INSERT INTO attendance_cases
    (
        employee_id,
        case_type,
        source,
        title,
        description,
        created_by_user_id,
        created_at
    )
    VALUES
    (
        ?,
        ?,
        'manager_created',
        ?,
        ?,
        ?,
        NOW()
    )
");

$stmt->bind_param(
    'isssi',
    $employeeId,
    $caseType,
    $title,
    $description,
    $userId
);

$stmt->execute();

$caseId =
    $conn->insert_id;

audit_log(
    'attendance_case',
    'create',
    $caseId,
    $title . ' - ' . $description
);

$_SESSION['success'] =
    'Slučaj je uspešno otvoren.';

redirect(
    'modules/dashboard/index.php'
);