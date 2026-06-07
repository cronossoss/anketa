<?php

require_once '../../../config/init.php';

require_login();
require_role(['admin', 'manager']);

require_once '../helpers/organization.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

if (!verify_csrf($_POST['csrf_token'] ?? '')) {
    die('CSRF validation failed.');
}

$employeeId     = (int)($_POST['employee_id'] ?? 0);
$absenceTypeId  = (int)($_POST['absence_type_id'] ?? 0);
$dateFrom       = trim($_POST['date_from'] ?? '');
$dateTo         = trim($_POST['date_to'] ?? '');
$note           = trim($_POST['note'] ?? '');

if (
    !$employeeId
    || !$absenceTypeId
    || empty($dateFrom)
    || empty($dateTo)
) {
    header('Location: create.php?error=required');
    exit;
}

if (strtotime($dateFrom) > strtotime($dateTo)) {

    header('Location: create.php?error=dates');
    exit;
}

$managerEmployeeId = getManagerEmployeeId(
    $conn,
    $_SESSION['user_id']
);

$documentPath = null;

if (
    isset($_FILES['document'])
    && $_FILES['document']['error'] === UPLOAD_ERR_OK
) {

    $uploadDir =
        '../../../uploads/attendance/absences/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $extension = pathinfo(
        $_FILES['document']['name'],
        PATHINFO_EXTENSION
    );

    $fileName =
        uniqid('absence_', true)
        . '.'
        . $extension;

    $destination =
        $uploadDir . $fileName;

    if (
        move_uploaded_file(
            $_FILES['document']['tmp_name'],
            $destination
        )
    ) {
        $documentPath =
            'uploads/attendance/absences/'
            . $fileName;
    }
}

$stmt = $conn->prepare("

    INSERT INTO attendance_absences
    (
        employee_id,
        absence_type_id,
        date_from,
        date_to,
        note,
        approved_by,
        document_path,
        status,
        requested_by,
        approved_at,
        source
    )
    VALUES
    (
        ?, ?, ?, ?, ?,
        ?, ?, 'approved',
        ?, NOW(),
        'manager_created'
    )

");

$stmt->bind_param(
    'iisssisis',
    $employeeId,
    $absenceTypeId,
    $dateFrom,
    $dateTo,
    $note,
    $managerEmployeeId,
    $documentPath,
    $managerEmployeeId
);

$stmt->execute();

header('Location: index.php?success=1');
exit;
