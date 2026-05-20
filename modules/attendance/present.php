<?php

require_once '../../config/init.php';

$currentPage = 'attendance-present';

$pageTitle = "Trenutno prisutni";

include "../../layouts/admin_layout_start.php";

$date = date('Y-m-d');

$result = $conn->query("

    SELECT

        e.id,
        e.first_name,
        e.last_name,

        ou.code AS organizational_unit,
        ou.name AS organizational_unit_name,

        ads.first_in,
        ads.last_out,
        ads.late_minutes,
        ads.presence_status,
        ads.reason_label,
        ads.reason_type,
        ads.is_justified,
        ads.early_leave_minutes,


        (
            SELECT al.direction
            FROM attendance_logs al
            WHERE al.employee_id = e.id
            ORDER BY al.access_datetime DESC
            LIMIT 1
        ) AS last_direction

    FROM employees e

    LEFT JOIN organizational_units ou
        ON ou.id = e.organizational_unit_id

    LEFT JOIN attendance_daily_summary ads
        ON ads.employee_id = e.id
        AND ads.work_date = '$date'

    ORDER BY
        ou.name ASC,
        e.last_name ASC

");

$groupedData = [];

while ($row = $result->fetch_assoc()) {

    $ouCode =
        $row['organizational_unit']
        ?: 'Bez OJ';

    $ouName =
        $row['organizational_unit_name']
        ?: '';

    $ou =
        $ouCode . '|' . $ouName;

    $groupedData[$ou][] = $row;
}

?>

<div class="container-fluid">

    <div class="card shadow-sm border-0">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h4 class="mb-1">
                        Dnevni pregled prisustva
                    </h4>

                    <div class="text-muted">

                        Pregled dolazaka, izlazaka, kašnjenja i opravdanih odsustava

                    </div>

                </div>

                <div>

                    <span class="badge bg-success fs-6">

                        Uživo

                    </span>

                </div>

            </div>

            <table class="table table-hover align-middle">

                <thead>

                    <tr>

                        <th>Zaposleni</th>

                        <th>OJ</th>

                        <th>

                            <i class="bi bi-box-arrow-in-right text-success"></i>

                            Ulaz

                        </th>

                        <th>

                            <i class="bi bi-box-arrow-left text-danger"></i>

                            Izlaz

                        </th>

                        <th>

                            <i class="bi bi-clock-history text-warning"></i>

                            Kašnjenje

                        </th>

                        <th>

                            <i class="bi bi-door-open text-primary"></i>

                            Razlog izlaza

                        </th>

                        <th>

                            <i class="bi bi-person-check"></i>

                            Status

                        </th>

                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($groupedData as $ou => $employees): ?>
                        <?php

                        [$ouCode, $ouName] =
                            explode('|', $ou);

                        ?>

                        <?php

                        $collapseId =
                            'ou_' . md5($ou);

                        $presentCount = 0;
                        $lateCount = 0;
                        $justifiedCount = 0;
                        $outCount = 0;
                        $absentCount = 0;

                        foreach ($employees as $emp) {

                            $status =
                                $emp['presence_status'];

                            $lastDirection =
                                $emp['last_direction'];

                            if (
                                $status === 'present'
                                &&
                                $lastDirection === 'IN'
                            ) {

                                $presentCount++;
                            } elseif (
                                $status === 'late'
                                &&
                                $lastDirection === 'IN'
                            ) {

                                $lateCount++;
                            } elseif (
                                $emp['is_justified']
                            ) {

                                $justifiedCount++;
                            } elseif (
                                $lastDirection === 'OUT'
                            ) {

                                $outCount++;
                            } else {

                                $absentCount++;
                            }
                        }

                        ?>

                        <!-- OJ HEADER -->

                        <tr
                            class="table-dark"
                            data-bs-toggle="collapse"
                            data-bs-target=".<?= $collapseId ?>"
                            style="cursor:pointer;">

                            <td colspan="7">

                                <div class="d-flex justify-content-between align-items-center">

                                    <div>

                                        <strong>

                                            <div class="d-flex align-items-center gap-3">

                                                <span class="fw-bold">

                                                    <?= htmlspecialchars($ouCode) ?>

                                                </span>

                                                <span class="text-light opacity-75">

                                                    <?= htmlspecialchars($ouName) ?>

                                                </span>

                                            </div>

                                        </strong>

                                    </div>

                                    <div class="d-flex gap-2">

                                        <span class="badge bg-success">

                                            🟢 <?= $presentCount ?>

                                        </span>

                                        <span class="badge bg-warning text-dark">

                                            🟡 <?= $lateCount ?>

                                        </span>

                                        <span class="badge bg-info text-dark">

                                            🔵 <?= $justifiedCount ?>

                                        </span>

                                        <span class="badge bg-secondary">

                                            ⚪ <?= $outCount ?>

                                        </span>

                                        <span class="badge bg-danger">

                                            🔴 <?= $absentCount ?>

                                        </span>

                                    </div>

                                </div>

                            </td>

                        </tr>

                        <!-- ZAPOSLENI -->

                        <?php foreach ($employees as $row): ?>

                            <tr class="collapse show <?= $collapseId ?>">

                                <td>

                                    <strong>

                                        <?= htmlspecialchars(
                                            $row['first_name']
                                                . ' ' .
                                                $row['last_name']
                                        ) ?>

                                    </strong>

                                </td>

                                <td>

                                    <?= htmlspecialchars(
                                        $row['organizational_unit']
                                            ?? '-'
                                    ) ?>

                                </td>

                                <td>

                                    <?= $row['first_in']
                                        ? date(
                                            'H:i',
                                            strtotime($row['first_in'])
                                        )
                                        : '-' ?>

                                </td>

                                <td>

                                    <?= $row['last_out']
                                        ? date(
                                            'H:i',
                                            strtotime($row['last_out'])
                                        )
                                        : '-' ?>

                                </td>

                                <td>

                                    <?php if (
                                        $row['late_minutes'] > 0
                                    ): ?>

                                        <span class="badge bg-warning text-dark">

                                            <?= $row['late_minutes'] ?>
                                            min

                                        </span>

                                    <?php else: ?>

                                        <span class="text-muted">

                                            -

                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td>

                                    <?php if (
                                        $row['reason_label']
                                    ): ?>

                                        <span class="badge bg-info text-dark">

                                            <?= htmlspecialchars(
                                                $row['reason_label']
                                            ) ?>

                                        </span>

                                    <?php elseif (
                                        $row['early_leave_minutes'] > 0
                                    ): ?>

                                        <span class="badge bg-danger">

                                            Nedefinisano

                                        </span>

                                    <?php else: ?>

                                        <span class="text-muted">

                                            -

                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td>

                                    <?php

                                    $status =
                                        $row['presence_status'];

                                    $lastDirection =
                                        $row['last_direction'];

                                    ?>

                                    <?php if (

                                        $row['is_justified']
                                        &&
                                        $row['last_out']

                                    ): ?>

                                        <span class="badge bg-info text-dark">

                                            Opravdano

                                        </span>

                                    <?php elseif (
                                        $status === 'present'
                                        &&
                                        $lastDirection === 'IN'
                                    ): ?>

                                        <span class="badge bg-success">

                                            Prisutan

                                        </span>

                                    <?php elseif (

                                        (
                                            $status === 'present'
                                            ||
                                            $status === 'late'
                                        )

                                        &&

                                        $lastDirection === 'OUT'

                                    ): ?>

                                        <span class="badge bg-secondary">

                                            Otišao

                                        </span>

                                    <?php elseif (
                                        $status === 'late'
                                        &&
                                        $lastDirection === 'IN'
                                    ): ?>

                                        <span class="badge bg-warning text-dark">

                                            Kasni

                                        </span>

                                    <?php elseif (
                                        $status === 'vacation'
                                    ): ?>

                                        <span class="badge bg-primary">

                                            Godišnji

                                        </span>

                                    <?php elseif (
                                        $status === 'doctor'
                                    ): ?>

                                        <span class="badge bg-info text-dark">

                                            Lekar

                                        </span>

                                    <?php else: ?>

                                        <span class="badge bg-danger">

                                            Odsutan

                                        </span>

                                    <?php endif; ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<?php include "../../layouts/admin_layout_end.php"; ?>