<?php

require_once '../../../config/init.php';
require_once '../../../helpers/audit.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$userId = $_SESSION['user_id'] ?? 0;

/*
|--------------------------------------------------------------------------
| Employee ID iz baze (NE iz POST-a)
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT employee_id
    FROM users
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param('i', $userId);
$stmt->execute();

$user = $stmt
    ->get_result()
    ->fetch_assoc();

if (!$user || empty($user['employee_id'])) {

    $_SESSION['error'] =
        'Nije pronađen zaposleni povezan sa korisnikom.';

    header('Location: create.php');
    exit;
}

$employeeId = (int)$user['employee_id'];

/*
|--------------------------------------------------------------------------
| POST podaci
|--------------------------------------------------------------------------
*/

$exitTypeId =
    (int)($_POST['exit_type_id'] ?? 0);

$dateFrom =
    trim($_POST['date_from'] ?? '');

$dateTo =
    trim($_POST['date_to'] ?? '');

$note =
    trim($_POST['note'] ?? '');

/*
|--------------------------------------------------------------------------
| Validacija
|--------------------------------------------------------------------------
*/

if (
    !$exitTypeId ||
    empty($dateFrom) ||
    empty($dateTo)
) {
    $_SESSION['error'] =
        'Popunite sva obavezna polja.';

    header('Location: create.php');
    exit;
}

$dateFromSql =
    date('Y-m-d H:i:s', strtotime($dateFrom));

$dateToSql =
    date('Y-m-d H:i:s', strtotime($dateTo));

if (
    strtotime($dateToSql)
    <=
    strtotime($dateFromSql)
) {
    $_SESSION['error'] =
        'Vreme povratka mora biti nakon vremena izlaska.';

    header('Location: create.php');
    exit;
}

$stmt = $conn->prepare("
    SELECT id
    FROM attendance_exit_types
    WHERE id = ?
      AND active = 1
    LIMIT 1
");

$stmt->bind_param('i', $exitTypeId);
$stmt->execute();

if (!$stmt->get_result()->num_rows) {

    $_SESSION['error'] =
        'Izabrani tip izlaska nije validan.';

    header('Location: create.php');
    exit;
}

$stmt = $conn->prepare("
    SELECT id
    FROM attendance_exit_passes
    WHERE employee_id = ?
      AND status IN ('pending','approved')
      AND (
            date_from < ?
        AND date_to   > ?
      )
    LIMIT 1
");

$stmt->bind_param(
    'iss',
    $employeeId,
    $dateToSql,
    $dateFromSql
);

$stmt->execute();

if ($stmt->get_result()->num_rows > 0) {

    $_SESSION['error'] =
        'Već postoji izlaznica u izabranom periodu.';

    header('Location: create.php');
    exit;
}

$stmt->bind_param(
    'issss',
    $employeeId,
    $dateFromSql,
    $dateToSql,
    $dateFromSql,
    $dateToSql
);

$stmt->execute();

if ($stmt->get_result()->num_rows > 0) {

    $_SESSION['error'] =
        'Već postoji izlaznica u tom periodu.';

    header('Location: create.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| Upis zahteva
|--------------------------------------------------------------------------
*/

$status = 'pending';
$source = 'employee_request';

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
        requested_by
    )
    VALUES
    (
        ?, ?, ?, ?, ?, ?, ?, ?
    )
");

if (!$stmt) {
    die('Prepare error: ' . $conn->error);
}

$stmt->bind_param(
    'iisssssi',
    $employeeId,
    $exitTypeId,
    $dateFromSql,
    $dateToSql,
    $note,
    $status,
    $source,
    $userId
);

if (!$stmt->execute()) {
    die('Execute error: ' . $stmt->error);
}

$passId = $conn->insert_id;

/*
|--------------------------------------------------------------------------
| Audit
|--------------------------------------------------------------------------
*/

audit_log(
    'attendance_exit_passes',
    'create',
    $passId,
    "Kreiran zahtev za izlaznicu"
);

/*
|--------------------------------------------------------------------------
| Success
|--------------------------------------------------------------------------
*/

$_SESSION['success'] =
    'Zahtev za izlaznicu je uspešno poslat.';

header('Location: index.php');
exit;