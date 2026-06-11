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

$absenceId =
    (int)($_POST['absence_id'] ?? 0);

$closureNote =
    trim(
        $_POST['closure_note']
        ?? ''
    );

if (
    !$absenceId ||
    !$closureNote
) {
    exit('Nedostaju podaci');
}

$stmt = $conn->prepare("
    UPDATE attendance_absences
    SET
        closed_at = NOW(),
        closed_by_user_id = ?,
        closure_note = ?
    WHERE id = ?
");

$stmt->bind_param(
    'isi',
    $_SESSION['user_id'],
    $closureNote,
    $absenceId
);

$stmt->execute();

audit_log(
    'attendance_absence',
    'close_case',
    $absenceId,
    $closureNote
);

$_SESSION['success'] =
    'Slučaj je zatvoren.';

redirect(
    'modules/dashboard/index.php'
);