<?php

require_once '../../../config/init.php';

require_login();
require_role(['manager', 'admin']);

require_once '../../../helpers/format.php';

$pageTitle = 'Zahtevi zaposlenih';

include "../../../layouts/admin_layout_start.php";

$currentEmployeeId = null;

if (!has_role('admin')) {

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

    $user = $stmt
        ->get_result()
        ->fetch_assoc();

    $currentEmployeeId =
        (int)($user['employee_id'] ?? 0);
}

$requests = [];

if (has_role('admin')) {

    $absenceFilter = '';
    $exitFilter = '';
} else {

    $absenceFilter =
        ' AND aa.approver_employee_id = '
        . (int)$currentEmployeeId;

    $exitFilter =
        ' AND ep.approver_employee_id = '
        . (int)$currentEmployeeId;
}

$sql = "

    SELECT

        aa.id,
        aa.created_at,

        'absence' AS request_type,

        e.id AS employee_id,

        e.first_name,
        e.last_name,

        approver.first_name AS approver_first_name,
        approver.last_name  AS approver_last_name,

        ou.code AS organizational_units,

        aat.name AS request_name,

        aa.date_from,
        aa.date_to,

        aa.status

    FROM attendance_absences aa

    JOIN employees e
        ON e.id = aa.employee_id

    LEFT JOIN organizational_units ou
        ON ou.id = e.organizational_unit_id

    LEFT JOIN employees approver
        ON approver.id = aa.approver_employee_id

    JOIN attendance_absence_types aat
        ON aat.id = aa.absence_type_id

    WHERE aa.status = 'pending'

    $absenceFilter

";

$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {

    $requests[] = $row;
}

$sql = "

    SELECT

        ep.id,
        ep.created_at,

        'exit' AS request_type,

        e.id AS employee_id,

        e.first_name,
        e.last_name,

        approver.first_name AS approver_first_name,
        approver.last_name  AS approver_last_name,

        ou.code AS organizational_units,

        et.name AS request_name,

        ep.date_from,
        ep.date_to,

        ep.status

    FROM attendance_exit_passes ep

    JOIN employees e
        ON e.id = ep.employee_id

    LEFT JOIN organizational_units ou
        ON ou.id = e.organizational_unit_id

    LEFT JOIN employees approver
        ON approver.id = ep.approver_employee_id

    JOIN attendance_exit_types et
        ON et.id = ep.exit_type_id

    WHERE ep.status = 'pending'

    $exitFilter

";

$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {

    $requests[] = $row;
}

usort(
    $requests,
    fn($a, $b)
    =>
    strtotime($b['created_at'])
        <=>
        strtotime($a['created_at'])
);

?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="mb-1">
                Zahtevi zaposlenih
            </h3>

            <div class="text-muted">
                Zahtevi koji čekaju odobrenje
            </div>

        </div>

    </div>

    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead>

                        <tr>

                            <th>Zaposleni</th>
                            <th>Vrsta</th>
                            <th>Tip</th>
                            <th>Period</th>
                            <th>Odobrava</th>
                            <th>Akcije</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($requests as $row): ?>

                            <tr>

                                <td>

                                    <strong>

                                        (<?= htmlspecialchars($row['organizational_units']) ?>)

                                        <?= htmlspecialchars(
                                            $row['last_name']
                                                . ' '
                                                . $row['first_name']
                                        ) ?>

                                    </strong>

                                </td>

                                <td>

                                    <?php if ($row['request_type'] === 'absence'): ?>

                                        <span class="badge bg-primary">

                                            Odsustvo

                                        </span>

                                    <?php else: ?>

                                        <span class="badge bg-info text-dark">

                                            Izlaznica

                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td>

                                    <?= htmlspecialchars(
                                        $row['request_name']
                                    ) ?>

                                </td>

                                <td>

                                    <?= sr_datetime(
                                        $row['date_from']
                                    ) ?>

                                    <br>

                                    <small class="text-muted">

                                        do

                                        <?= sr_datetime(
                                            $row['date_to']
                                        ) ?>

                                    </small>

                                </td>

                                <td>

                                    <?php if (!empty($row['approver_last_name'])): ?>

                                        <span class="badge bg-secondary">

                                            <?= htmlspecialchars(
                                                $row['approver_last_name']
                                                    . ' '
                                                    . $row['approver_first_name']
                                            ) ?>

                                        </span>

                                    <?php else: ?>

                                        <span class="badge bg-danger">
                                            Nije određeno
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td>

                                    <div class="btn-group btn-group-sm">

                                        <a
                                            href="approve.php?type=<?= $row['request_type'] ?>&id=<?= $row['id'] ?>"
                                            class="btn btn-success">

                                            <i class="bi bi-check-lg"></i>

                                        </a>

                                        <a
                                            href="reject.php?type=<?= $row['request_type'] ?>&id=<?= $row['id'] ?>"
                                            class="btn btn-danger">

                                            <i class="bi bi-x-lg"></i>

                                        </a>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>