<?php

require_once '../../../config/init.php';

require_login();

$pageTitle = 'Moje prisustvo';

$currentPage = 'my-attendance';

include "../../../layouts/admin_layout_start.php";

$userId =
    $_SESSION['user_id'];

$stmt = $conn->prepare("

    SELECT employee_id

    FROM users

    WHERE id = ?

    LIMIT 1

");

$stmt->bind_param(
    'i',
    $userId
);

$stmt->execute();

$employeeId =
    $stmt
        ->get_result()
        ->fetch_assoc()['employee_id']
    ?? 0;

$month =
    $_GET['month']
    ?? date('Y-m');

//
// KPI
//

$kpi = $conn->prepare("

    SELECT

        COUNT(
            CASE
                WHEN presence_status IN (
                    'present',
                    'late',
                    'approved_exit'
                )
                THEN 1
            END
        ) AS present_days,

        COUNT(
            CASE
                WHEN late_minutes > 0
                THEN 1
            END
        ) AS late_days,

        COUNT(
            CASE
                WHEN early_leave_minutes > 0
                THEN 1
            END
        ) AS early_leave_days,

        SUM(worked_minutes)
            AS worked_minutes

    FROM attendance_daily_summary

    WHERE employee_id = ?

    AND DATE_FORMAT(
        work_date,
        '%Y-%m'
    ) = ?

");

$kpi->bind_param(
    'is',
    $employeeId,
    $month
);

$kpi->execute();

$stats =
    $kpi
    ->get_result()
    ->fetch_assoc();

//
// TABELA
//

$list = $conn->prepare("

    SELECT *

    FROM attendance_daily_summary

    WHERE employee_id = ?

    AND DATE_FORMAT(
        work_date,
        '%Y-%m'
    ) = ?

    ORDER BY work_date DESC

");

$list->bind_param(
    'is',
    $employeeId,
    $month
);

$list->execute();

$result =
    $list
    ->get_result();

$statusLabels = [

    'present'       => 'Prisutan',
    'late'          => 'Kašnjenje',
    'approved_exit' => 'Opravdan izlaz',
    'vacation'      => 'Godišnji',
    'sick_leave'    => 'Bolovanje',
    'business_trip' => 'Službeni put',
    'remote_work'   => 'Rad od kuće',
    'absent'        => 'Odsutan'

];

$statusClass = [

    'present'       => 'bg-success',
    'late'          => 'bg-warning text-dark',
    'approved_exit' => 'bg-info',
    'vacation'      => 'bg-primary',
    'sick_leave'    => 'bg-danger',
    'business_trip' => 'bg-dark',
    'remote_work'   => 'bg-secondary',
    'absent'        => 'bg-danger'

];



?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="mb-1">

                Moje prisustvo

            </h3>

            <div class="text-muted">

                Pregled rada i prisustva

            </div>

        </div>

        <form method="GET">

            <input
                type="month"
                name="month"
                value="<?= htmlspecialchars($month) ?>"
                class="form-control"
                onchange="this.form.submit()">

        </form>

    </div>

    <div class="row mb-4">

        <div class="col-md-3">

            <div class="card shadow-sm border-0">

                <div class="card-body">

                    <div class="text-muted">

                        Prisutni dani

                    </div>

                    <div class="fs-2 fw-bold text-success">

                        <?= (int)$stats['present_days'] ?>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-md-3">

            <div class="card shadow-sm border-0">

                <div class="card-body">

                    <div class="text-muted">

                        Kašnjenja

                    </div>

                    <div class="fs-2 fw-bold text-warning">

                        <?= (int)$stats['late_days'] ?>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-md-3">

            <div class="card shadow-sm border-0">

                <div class="card-body">

                    <div class="text-muted">

                        Raniji izlazi

                    </div>

                    <div class="fs-2 fw-bold text-danger">

                        <?= (int)$stats['early_leave_days'] ?>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-md-3">

            <div class="card shadow-sm border-0">

                <div class="card-body">

                    <div class="text-muted">

                        Radni sati

                    </div>

                    <div class="fs-2 fw-bold text-primary">

                        <?= round(
                            ($stats['worked_minutes'] ?? 0) / 60,
                            1
                        ) ?> h

                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead>

                        <tr>

                            <th>Datum</th>
                            <th>Dolazak</th>
                            <th>Odlazak</th>
                            <th>Rad</th>
                            <th>Prekovremeno</th>
                            <th>Kašnjenje</th>
                            <th>Status</th>
                            <th>Napomena</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php while ($row = $result->fetch_assoc()): ?>

                            <tr>

                                <td>

                                    <?= sr_date($row['work_date']) ?>

                                </td>

                                <td>

                                    <?= $row['first_in']
                                        ? date(
                                            'H:i',
                                            strtotime(
                                                $row['first_in']
                                            )
                                        )
                                        : '-' ?>

                                </td>

                                <td>

                                    <?= $row['last_out']
                                        ? date(
                                            'H:i',
                                            strtotime(
                                                $row['last_out']
                                            )
                                        )
                                        : '-' ?>

                                </td>

                                <td>

                                    <?php
                                    $hours = floor($row['regular_minutes'] / 60);
                                    $minutes = $row['regular_minutes'] % 60;
                                    ?>

                                    <?= "{$hours}h {$minutes}m" ?>

                                </td>

                                <td>

                                    <?php

                                    $overtimeMinutes =
                                        (int)$row['overtime_minutes'];

                                    $otHours =
                                        floor($overtimeMinutes / 60);

                                    $remainder =
                                        $overtimeMinutes % 60;

                                    if ($remainder >= 45) {

                                        $otHours++;
                                    }

                                    ?>

                                    <?= $otHours > 0 ? $otHours . ' h' : '-' ?>

                                </td>
                                <td>

                                    <?php if ($row['late_minutes'] > 0): ?>

                                        <span class="badge bg-warning text-dark">

                                            <?= $row['late_minutes'] ?> min

                                        </span>

                                    <?php else: ?>

                                        <span class="text-muted">-</span>

                                    <?php endif; ?>

                                </td>

                                <td>

                                    <span class="badge <?= $statusClass[$row['presence_status']] ?? 'bg-secondary' ?>">

                                        <?= htmlspecialchars(

                                            $statusLabels[$row['presence_status']]

                                                ??

                                                $row['presence_status']

                                        ) ?>

                                    </span>

                                </td>

                                <td>

                                    <?= htmlspecialchars(
                                        $row['reason_label'] ?? '-'
                                    ) ?>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

<?php include "../../../layouts/admin_layout_end.php"; ?>