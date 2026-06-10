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

$resolutionType =
    trim($_POST['resolution_type'] ?? '');

$reason =
    trim($_POST['reason'] ?? '');

if (
    !$employeeId ||
    !$resolutionType ||
    !$reason
) {
    exit('Nedostaju podaci');
}

$userId =
    (int)$_SESSION['user_id'];

$conn->begin_transaction();

try {

    switch ($resolutionType) {

        /*
        |--------------------------------------------------------------------------
        | PRISUTAN
        |--------------------------------------------------------------------------
        */

        case 'present':

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
                    CURDATE(),
                    CURTIME(),
                    ?,
                    ?,
                    ?,
                    'approved'
                )
            ");

            $stmt->bind_param(
                'isii',
                $employeeId,
                $reason,
                $userId,
                $userId
            );

            $stmt->execute();

            $correctionId =
                $conn->insert_id;

            $rawData =
                json_encode([
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
                    NOW(),
                    'IN',
                    'Manager resolution',
                    'manager_resolution',
                    ?,
                    1
                )
            ");

            $stmt->bind_param(
                'is',
                $employeeId,
                $rawData
            );

            $stmt->execute();

            break;

        /*
        |--------------------------------------------------------------------------
        | ODSUSTVA
        |--------------------------------------------------------------------------
        */

        default:

            $categoryMap = [

                'vacation' =>
                    'vacation',

                'doctor' =>
                    'doctor',

                'business_trip' =>
                    'business_trip',

                'unpaid_leave' =>
                    'unpaid_leave'
            ];

            if (
                $resolutionType ===
                'unexcused'
            ) {

                $stmt =
                    $conn->prepare("
                        SELECT id
                        FROM attendance_absence_types
                        WHERE name =
                              'Neopravdani izostanak'
                        LIMIT 1
                    ");

            } else {

                $category =
                    $categoryMap[
                        $resolutionType
                    ] ?? '';

                $stmt =
                    $conn->prepare("
                        SELECT id
                        FROM attendance_absence_types
                        WHERE category = ?
                        LIMIT 1
                    ");

                $stmt->bind_param(
                    's',
                    $category
                );
            }

            $stmt->execute();

            $absenceTypeId =
                (int)$stmt
                    ->get_result()
                    ->fetch_assoc()['id'];

            if (!$absenceTypeId) {

                throw new Exception(
                    'Tip odsustva nije pronađen.'
                );
            }

        $followUpRequired = in_array(
                $resolutionType,
                [
                    'doctor',
                    'business_trip',
                    'sick_leave'
                ]
            ) ? 1 : 0;

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
                    follow_up_required,
                    requested_by,
                    approver_employee_id,
                    approved_by_employee_id,
                    approved_at
                )
                VALUES
                (
                    ?,
                    ?,
                    NOW(),
                    NOW(),
                    ?,
                    'approved',
                    'manager_created',
                    'manager_resolution',
                    ?,
                    ?,
                    NULL,
                    NULL,
                    ?,
                    NOW()
                )
            ");    

            $managerEmployeeId =
                (int)$_SESSION['employee_id'];

            $userId =
                (int)$_SESSION['user_id'];

            $stmt->bind_param(
                'iisiii',
                $employeeId,
                $absenceTypeId,
                $reason,
                $userId,
                $followUpRequired,
                $managerEmployeeId
            );

            $stmt->execute();

            break;
    }

    audit_log(
        'attendance_resolution',
        $resolutionType,
        $employeeId,
        $reason
    );

    $conn->commit();

    $_SESSION['success'] =
        'Status zaposlenog je uspešno razrešen.';
}
catch (Throwable $e) {

    $conn->rollback();

    $_SESSION['error'] =
        $e->getMessage();
}

redirect(
    'modules/dashboard/index.php'
);