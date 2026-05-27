<?php

require_once '../../../config/init.php';

$currentPage =
    'attendance-weekly-report';

$pageTitle =
    'Nedeljni izveštaj prisustva';

include "../../../layouts/admin_layout_start.php";

//
// FILTERI
//

$startDate =
    $_GET['start_date']
    ?? date(
        'Y-m-d',
        strtotime('monday this week')
    );

$endDate =
    date(
        'Y-m-d',
        strtotime(
            $startDate . ' +6 days'
        )
    );

$organizationalUnit =
    $_GET['organizational_unit']
    ?? '';

//
// OJ LISTA
//

$ouResult = $conn->query("

    SELECT

        id,
        code,
        name

    FROM organizational_units

    ORDER BY code ASC

");

//
// WHERE
//

$where = "

    ads.work_date BETWEEN
    '$startDate'
    AND
    '$endDate'

";

if ($organizationalUnit) {

    $where .= "

        AND ou.id =
        " . (int)$organizationalUnit;
}

//
// QUERY
//

$query = "

    SELECT

        e.id,

        e.first_name,
        e.last_name,

        ou.code AS organizational_unit,

        COUNT(
            CASE
                WHEN ads.presence_status
                IN ('present', 'late')
                THEN 1
            END
        ) AS work_days,

        SUM(
            ads.late_minutes
        ) AS total_late_minutes,

        SUM(
            ads.overtime_minutes
        ) AS total_overtime_minutes,

        SUM(
            ads.early_leave_minutes
        ) AS total_early_leave_minutes,

        COUNT(
            CASE
                WHEN ads.is_justified = 1
                THEN 1
            END
        ) AS justified_absences,

        COUNT(
            CASE
                WHEN ads.presence_status = 'absent'
                THEN 1
            END
        ) AS absent_days

    FROM attendance_daily_summary ads

    JOIN employees e
        ON e.id = ads.employee_id

    LEFT JOIN organizational_units ou
        ON ou.id = e.organizational_unit_id

    WHERE $where

    GROUP BY e.id

    ORDER BY

        ou.code ASC,
        e.last_name ASC

";

$result =
    $conn->query($query);

//
// KPI
//

$totalEmployees = 0;

$totalLate = 0;

$totalOvertime = 0;

$totalAbsences = 0;

$data = [];

while ($row = $result->fetch_assoc()) {

    $data[] = $row;

    $totalEmployees++;

    $totalLate +=
        $row['total_late_minutes'];

    $totalOvertime +=
        $row['total_overtime_minutes'];

    $totalAbsences +=
        $row['absent_days'];
}

?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="mb-1">

                Nedeljni izveštaj prisustva

            </h3>

            <div class="text-muted">

                Period:

                <?= date(
                    'd.m.Y',
                    strtotime($startDate)
                ) ?>

                -

                <?= date(
                    'd.m.Y',
                    strtotime($endDate)
                ) ?>

            </div>

        </div>

    </div>

    <!-- FILTER -->

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <form method="GET">

                <div class="row g-3 align-items-end">

                    <div class="col-md-3">

                        <label class="form-label">

                            Početak nedelje

                        </label>

                        <input
                            type="date"
                            name="start_date"
                            class="form-control"
                            value="<?= $startDate ?>"
                        >

                    </div>

                    <div class="col-md-4">

                        <label class="form-label">

                            Organizaciona jedinica

                        </label>

                        <select
                            name="organizational_unit"
                            class="form-select"
                        >

                            <option value="">

                                Sve organizacione jedinice

                            </option>

                            <?php while ($ou = $ouResult->fetch_assoc()): ?>

                                <option
                                    value="<?= $ou['id'] ?>"
                                    <?= $organizationalUnit == $ou['id']
                                        ? 'selected'
                                        : '' ?>
                                >

                                    <?= htmlspecialchars(
                                        $ou['code']
                                        . ' - '
                                        . $ou['name']
                                    ) ?>

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>

                    <div class="col-md-2">

                        <button
                            type="submit"
                            class="btn btn-primary w-100"
                        >

                            <i class="bi bi-search me-1"></i>

                            Prikaži

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

    <!-- KPI -->

    <div class="row mb-4">

        <div class="col-md-3">

            <div class="card border-0 shadow-sm">

                <div class="card-body">

                    <div class="text-muted">

                        Radnici

                    </div>

                    <div class="fs-2 fw-bold">

                        <?= $totalEmployees ?>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-md-3">

            <div class="card border-0 shadow-sm">

                <div class="card-body">

                    <div class="text-muted">

                        Ukupno kašnjenje

                    </div>

                    <div class="fs-2 fw-bold text-warning">

                        <?= $totalLate ?>
                        min

                    </div>

                </div>

            </div>

        </div>

        <div class="col-md-3">

            <div class="card border-0 shadow-sm">

                <div class="card-body">

                    <div class="text-muted">

                        Overtime

                    </div>

                    <div class="fs-2 fw-bold text-success">

                        <?= round(
                            $totalOvertime / 60,
                            1
                        ) ?>h

                    </div>

                </div>

            </div>

        </div>

        <div class="col-md-3">

            <div class="card border-0 shadow-sm">

                <div class="card-body">

                    <div class="text-muted">

                        Odsustva

                    </div>

                    <div class="fs-2 fw-bold text-danger">

                        <?= $totalAbsences ?>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- TABELA -->

    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead>

                        <tr>

                            <th>Zaposleni</th>
                            <th>OJ</th>
                            <th>Dani rada</th>
                            <th>Kašnjenje</th>
                            <th>Overtime</th>
                            <th>Raniji izlazak</th>
                            <th>Opravdano</th>
                            <th>Odsustva</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($data as $row): ?>

                            <tr>

                                <td>

                                    <strong>

                                        <?= htmlspecialchars(
                                            $row['last_name']
                                            . ' '
                                            . $row['first_name']
                                        ) ?>

                                    </strong>

                                </td>

                                <td>

                                    <?= htmlspecialchars(
                                        $row['organizational_unit']
                                    ) ?>

                                </td>

                                <td>

                                    <?= $row['work_days'] ?>

                                </td>

                                <td>

                                    <?= $row['total_late_minutes'] ?>
                                    min

                                </td>

                                <td>

                                    <?= round(
                                        $row['total_overtime_minutes'] / 60,
                                        1
                                    ) ?>h

                                </td>

                                <td>

                                    <?= $row['total_early_leave_minutes'] ?>
                                    min

                                </td>

                                <td>

                                    <?= $row['justified_absences'] ?>

                                </td>

                                <td>

                                    <?= $row['absent_days'] ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

<?php include "../../../layouts/admin_layout_end.php"; ?>