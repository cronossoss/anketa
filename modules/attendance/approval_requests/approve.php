<?php

require_once '../../../config/init.php';

require_login();
require_role(['manager', 'admin']);

$type = $_GET['type'] ?? '';
$id   = (int)($_GET['id'] ?? 0);

if (!$id) {

    header('Location: index.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| Trenutni zaposleni
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT employee_id
    FROM users
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param(
    'i',
    $_SESSION['user_id']
);

$stmt->execute();

$currentEmployeeId =
    (int)(
        $stmt
            ->get_result()
            ->fetch_assoc()['employee_id']
        ?? 0
    );

if (!$currentEmployeeId && !has_role('admin')) {

    $_SESSION['error'] =
        'Nije pronađen zaposleni.';

    header('Location: index.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| ODSUSTVO
|--------------------------------------------------------------------------
*/

if ($type === 'absence') {

    $stmt = $conn->prepare("
        SELECT
            approver_employee_id
        FROM attendance_absences
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->bind_param(
        'i',
        $id
    );

    $stmt->execute();

    $request =
        $stmt
        ->get_result()
        ->fetch_assoc();

    if (
        !$request
    ) {

        $_SESSION['error'] =
            'Zahtev nije pronađen.';

        header('Location: index.php');
        exit;
    }

    if (
        !has_role('admin')
        &&
        (int)$request['approver_employee_id']
        !==
        $currentEmployeeId
    ) {

        http_response_code(403);
        exit('403 Forbidden');
    }

    $stmt = $conn->prepare("
        UPDATE attendance_absences
        SET
            status = 'approved',
            approved_by = ?,
            approved_by_employee_id = ?,
            approved_at = NOW()
        WHERE id = ?
          AND status = 'pending'
    ");

    $stmt->bind_param(
        'iii',
        $currentEmployeeId,
        $currentEmployeeId,
        $id
    );

    $stmt->execute();

    $_SESSION['success'] =
        'Zahtev za odsustvo je odobren.';
}

/*
|--------------------------------------------------------------------------
| IZLAZNICA
|--------------------------------------------------------------------------
*/ elseif ($type === 'exit') {

    $stmt = $conn->prepare("
        SELECT
            approver_employee_id
        FROM attendance_exit_passes
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->bind_param(
        'i',
        $id
    );

    $stmt->execute();

    $request =
        $stmt
        ->get_result()
        ->fetch_assoc();

    if (
        !$request
    ) {

        $_SESSION['error'] =
            'Zahtev nije pronađen.';

        header('Location: index.php');
        exit;
    }

    if (
        !has_role('admin')
        &&
        (int)$request['approver_employee_id']
        !==
        $currentEmployeeId
    ) {

        http_response_code(403);
        exit('403 Forbidden');
    }

    $stmt = $conn->prepare("
        UPDATE attendance_exit_passes
        SET
            status = 'approved',
            approved_by = ?,
            approved_by_employee_id = ?,
            approved_at = NOW()
        WHERE id = ?
          AND status = 'pending'
    ");

    $stmt->bind_param(
        'iii',
        $currentEmployeeId,
        $currentEmployeeId,
        $id
    );

    $stmt->execute();

    $_SESSION['success'] =
        'Izlaznica je odobrena.';
}

header('Location: index.php');
exit;
