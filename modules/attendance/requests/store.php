<?php

require_once '../../../config/init.php';
require_once '../../../helpers/format.php';
require_once '../helpers/approval.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header('Location: index.php');
    exit;
}

if (!verify_csrf($_POST['csrf_token'] ?? '')) {

    $_SESSION['error'] = 'Neispravan CSRF token.';

    header('Location: create.php');
    exit;
}

$userId = $_SESSION['user_id'] ?? 0;

$stmt = $conn->prepare("
    SELECT employee_id
    FROM users
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param('i', $userId);
$stmt->execute();

if (!$stmt->execute()) {

    $_SESSION['error'] =
        'Greška pri snimanju zahteva: ' . $stmt->error;

    header('Location: create.php');
    exit;
}

$user = $stmt
    ->get_result()
    ->fetch_assoc();

if (!$user || !$user['employee_id']) {

    $_SESSION['error'] =
        'Korisnik nije povezan sa zaposlenim.';

    header('Location: create.php');
    exit;
}

$employeeId =
    (int)$user['employee_id'];

$approverEmployeeId =
    getApproverEmployeeId(
        $conn,
        $employeeId
    );

if (!$approverEmployeeId) {

    $_SESSION['error'] =
        'Nije pronađen nadređeni za odobravanje zahteva.';

    header('Location: create.php');
    exit;
}

$requestKind =
    $_POST['request_kind'] ?? '';

$note =
    trim($_POST['note'] ?? '');

if ($requestKind === 'exit') {

    $exitTypeId =
        (int)($_POST['exit_type_id'] ?? 0);

    $dateFrom =
        db_datetime(
            $_POST['exit_from'] ?? ''
        );

    $dateTo =
        db_datetime(
            $_POST['exit_to'] ?? ''
        );

    if (
        !$exitTypeId ||
        !$dateFrom ||
        !$dateTo
    ) {

        $_SESSION['error'] =
            'Popunite sva polja.';

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
        $dateTo,
        $dateFrom
    );

    $stmt->execute();

    if (!$stmt->execute()) {

        $_SESSION['error'] =
            'Greška pri snimanju zahteva: ' . $stmt->error;

        header('Location: create.php');
        exit;
    }

    if ($stmt->get_result()->num_rows > 0) {

        $_SESSION['error'] =
            'Već postoji izlaznica u izabranom periodu.';

        header('Location: create.php');
        exit;
    }

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
            requested_by,
            approver_employee_id
        )
        VALUES
        (
            ?, ?, ?, ?, ?, ?, ?, ?, ?
        )

    ");

    $stmt->bind_param(
        'iisssssii',
        $employeeId,
        $exitTypeId,
        $dateFrom,
        $dateTo,
        $note,
        $status,
        $source,
        $userId,
        $approverEmployeeId
    );

    $stmt->execute();

    if (!$stmt->execute()) {

        $_SESSION['error'] =
            'Greška pri snimanju zahteva: ' . $stmt->error;

        header('Location: create.php');
        exit;
    }

    $_SESSION['success'] =
        'Zahtev za izlaznicu je poslat.';

    header('Location: index.php');
    exit;
}

if ($requestKind === 'absence') {

    $absenceTypeId =
        (int)($_POST['absence_type_id'] ?? 0);

    $dateFrom =
        db_date(
            $_POST['absence_from'] ?? ''
        );

    $dateTo =
        db_date(
            $_POST['absence_to'] ?? ''
        );

    if (
        !$absenceTypeId ||
        !$dateFrom ||
        !$dateTo
    ) {

        $_SESSION['error'] =
            'Popunite sva polja.';

        header('Location: create.php');
        exit;
    }

    if ($absenceTypeId == 6) {

        $reason =
            trim(
                $_POST['paid_leave_reason']
                    ?? ''
            );

        $other =
            trim(
                $_POST['other_reason']
                    ?? ''
            );

        if ($reason === 'Drugo') {

            $reason = $other;
        }

        if (!empty($reason)) {

            $note =
                'Plaćeno odsustvo - '
                . $reason
                . PHP_EOL
                . PHP_EOL
                . $note;
        }
    }

    $dateFrom .= ' 00:00:00';
    $dateTo   .= ' 23:59:59';

    /*
|--------------------------------------------------------------------------
| Provera preklapanja odsustava
|--------------------------------------------------------------------------
*/

    $stmt = $conn->prepare("
    SELECT id
    FROM attendance_absences
    WHERE employee_id = ?
      AND status IN ('pending','approved')
      AND (
            date_from <= ?
        AND date_to   >= ?
      )
    LIMIT 1
");

    $stmt->bind_param(
        'iss',
        $employeeId,
        $dateTo,
        $dateFrom
    );

    $stmt->execute();

    if (!$stmt->execute()) {

        $_SESSION['error'] =
            'Greška pri snimanju zahteva: ' . $stmt->error;

        header('Location: create.php');
        exit;
    }

    if ($stmt->get_result()->num_rows > 0) {

        $_SESSION['error'] =
            'Već postoji zahtev za odsustvo u izabranom periodu.';

        header('Location: create.php');
        exit;
    }

    $status = 'pending';
    $source = 'employee_request';

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
            requested_by,
            approver_employee_id
        )
        VALUES
        (
            ?, ?, ?, ?, ?, ?, ?, ?, ?
        )

    ");

    $stmt->bind_param(
        'iisssssii',
        $employeeId,
        $absenceTypeId,
        $dateFrom,
        $dateTo,
        $note,
        $status,
        $source,
        $userId,
        $approverEmployeeId
    );

    $stmt->execute();

    if (!$stmt->execute()) {

        $_SESSION['error'] =
            'Greška pri snimanju zahteva: ' . $stmt->error;

        header('Location: create.php');
        exit;
    }

    $_SESSION['success'] =
        'Zahtev za odsustvo je poslat.';

    header('Location: index.php');
    exit;
}
