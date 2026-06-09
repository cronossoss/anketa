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

$reason =
    trim($_POST['reason'] ?? '');

$correctionDate =
    $_POST['correction_date']
    ?? date('Y-m-d');

$correctionTime =
    $_POST['correction_time']
    ?? '07:00';

if (
    !$employeeId ||
    empty($reason)
) {
    exit('Nedostaju podaci');
}

/*
|--------------------------------------------------------------------------
| DA LI VEĆ POSTOJI EVIDENCIJA ZA TAJ DAN
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT id
    FROM attendance_logs
    WHERE employee_id = ?
      AND DATE(access_datetime) = ?
    LIMIT 1
");

$stmt->bind_param(
    'is',
    $employeeId,
    $correctionDate
);

$stmt->execute();

if ($stmt->get_result()->num_rows > 0) {

    $_SESSION['warning'] =
        'Za izabrani datum već postoji evidencija.';

    redirect(
        'modules/dashboard/index.php'
    );

    exit;
}

/*
|--------------------------------------------------------------------------
| ATTENDANCE_CORRECTIONS
|--------------------------------------------------------------------------
*/

$userId =
    (int)$_SESSION['user_id'];

$stmt = $conn->prepare("
    INSERT INTO attendance_corrections
    (
        employee_id,
        correction_type,
        correction_date,
        correction_time,
        reason,
        created_by,
        approved_by,
        status
    )
    VALUES
    (
        ?,
        'arrival',
        ?,
        ?,
        ?,
        ?,
        ?,
        'approved'
    )
");

$stmt->bind_param(
    'isssii',
    $employeeId,
    $correctionDate,
    $correctionTime,
    $reason,
    $userId,
    $userId
);

$stmt->execute();

$correctionId =
    $conn->insert_id;

/*
|--------------------------------------------------------------------------
| ATTENDANCE_LOGS
|--------------------------------------------------------------------------
*/

$accessDateTime =
    $correctionDate .
    ' ' .
    $correctionTime .
    ':00';

$rawData = json_encode([
    'correction_id' => $correctionId,
    'reason' => $reason,
    'created_by' => $userId
]);

$stmt = $conn->prepare("
    INSERT INTO attendance_logs
    (
        employee_id,
        access_datetime,
        direction,
        terminal_name,
        source_system,
        raw_data,
        is_simulated
    )
    VALUES
    (
        ?,
        ?,
        'IN',
        'Manager correction',
        'manager_correction',
        ?,
        1
    )
");

$stmt->bind_param(
    'iss',
    $employeeId,
    $accessDateTime,
    $rawData
);

$stmt->execute();

/*
|--------------------------------------------------------------------------
| AUDIT
|--------------------------------------------------------------------------
*/

audit_log(
    'attendance_correction',
    'create',
    $correctionId,
    sprintf(
        'Korekcija dolaska. Zaposleni ID: %d. Datum: %s %s. Razlog: %s',
        $employeeId,
        $correctionDate,
        $correctionTime,
        $reason
    )
);

$_SESSION['success'] =
    'Korekcija uspešno sačuvana.';

redirect(
    'modules/dashboard/index.php'
);
