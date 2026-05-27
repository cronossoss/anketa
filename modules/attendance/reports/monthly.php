<?php

require_once '../../../config/init.php';

require_once '../../../helpers/attendance.php';


$currentPage =
    'attendance-monthly-report';

$pageTitle =
    'Mesečni izveštaj prisustva';

include "../../../layouts/admin_layout_start.php";

?>
<style>
    .table-sm td,
    .table-sm th {

        padding:
            0.45rem 0.55rem;

        vertical-align:
            middle;
    }
</style>
<?php

//
// FILTERI
//

$month =
    $_GET['month']
    ?? date('Y-m');

$organizationalUnit =
    $_GET['organizational_unit']
    ?? '';

//
// PERIOD
//

$startDate =
    $month . '-01';

$endDate =
    date(
        'Y-m-t',
        strtotime($startDate)
    );

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

        ROUND(

            SUM(
                ads.regular_minutes
            ) / 60,

            1

        ) AS regular_work_hours,

        SUM(
            ads.late_minutes
        ) AS total_late_minutes,

        SUM(
            ads.overtime_minutes
        ) AS total_overtime_minutes,

        ROUND(

            SUM(

                CASE

                    WHEN ads.reason_type
                    IN (15, 69, 47)

                    THEN ads.paid_leave_minutes

                    ELSE 0

                END

            ) / 60,

            1

        ) AS justified_leave_hours,

        SUM(
            ads.unjustified_early_leave_minutes
        ) AS unjustified_early_leave_minutes,

        SUM(
            ads.meal_allowance
        ) AS meal_days,

        SUM(
            ads.transport_allowance
        ) AS transport_days,

        ROUND(

            SUM(

                CASE

                    WHEN ads.reason_type = 9

                    THEN ads.paid_leave_minutes

                    ELSE 0

                END

            ) / 60,

            1

        ) AS vacation_hours,

        ROUND(

            SUM(

                CASE

                    WHEN ads.reason_type = 13

                    THEN ads.unpaid_leave_minutes

                    ELSE 0

                END

            ) / 60,

            1

        ) AS sick_leave_hours,

        ROUND(
            SUM(
                ads.paid_leave_minutes
            ) / 60,
            1
        ) AS paid_leave_hours,

        ROUND(
            SUM(
                ads.unpaid_leave_minutes
            ) / 60,
            1
        ) AS unpaid_leave_hours

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

$totalWorkedHours = 0;

$data = [];

while ($row = $result->fetch_assoc()) {

    $data[] = $row;

    $totalEmployees++;

    $totalLate +=
        $row['total_late_minutes'];

    $totalOvertime +=
        $row['total_overtime_minutes'];

    $totalAbsences +=
        $row['unpaid_leave_hours'];

    $totalWorkedHours +=
        $row['regular_work_hours'];
}

//
// PROCENAT PRISUSTVA
//

/* $workingDaysInMonth = 22;

$attendancePercent =
    $totalEmployees > 0
    ? round(
        (
            $totalWorkedDays
            /
            (
                $totalEmployees
                * $workingDaysInMonth
            )
        ) * 100
    )
    : 0; */

?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="mb-1">

                Mesečni izveštaj prisustva

            </h3>

            <div class="text-muted">

                <?= date(
                    'F Y',
                    strtotime($startDate)
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

                            Mesec

                        </label>

                        <input
                            type="month"
                            name="month"
                            class="form-control"
                            value="<?= $month ?>">

                    </div>

                    <div class="col-md-4">

                        <label class="form-label">

                            Organizaciona jedinica

                        </label>

                        <select
                            name="organizational_unit"
                            class="form-select">

                            <option value="">

                                Sve organizacione jedinice

                            </option>

                            <?php while ($ou = $ouResult->fetch_assoc()): ?>

                                <option
                                    value="<?= $ou['id'] ?>"
                                    <?= $organizationalUnit == $ou['id']
                                        ? 'selected'
                                        : '' ?>>

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
                            class="btn btn-primary w-100">

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

                        Regularan rad

                    </div>

                    <div class="fs-2 fw-bold text-success">

                        <?= round(
                            $totalWorkedHours,
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

                        Kašnjenja

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

                    <div class="fs-2 fw-bold text-primary">

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

                        Neplaćeno odsustvo

                    </div>

                    <div class="fs-2 fw-bold text-danger">

                        <?= round(
                            $totalAbsences,
                            1
                        ) ?>h

                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- TABELA -->

    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-sm table-hover align-middle">

                    <thead>

                        <tr>

                            <th>(OJ) Zaposleni</th>
                            <th>Rad</th>
                            <th>Kašnjenje</th>
                            <th>Prekovremeni<br>rad</th>
                            <th>Topli obrok</th>
                            <th>Prevoz</th>
                            <th>Opravdano</th>
                            <th>GO</th>
                            <th>Bolovanje</th>
                            <th>Neopravdano</th>
                            <th>Plaćeno</th>
                            <th>Neplaćeno</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($data as $row): ?>

                            <tr>

                                <td>

                                    <strong>

                                        (<?= htmlspecialchars(
                                                $row['organizational_unit']
                                            ) ?>)

                                        <?= htmlspecialchars(
                                            $row['last_name']
                                                . ' '
                                                . $row['first_name']
                                        ) ?>

                                    </strong>

                                </td>

                                <td>

                                    <?= $row['regular_work_hours'] ?? 0 ?>h

                                </td>

                                <td>

                                    <?= $row['total_late_minutes'] ?>
                                    min

                                </td>

                                <td>

                                    <?= calculate_overtime_hours(
                                        $row['total_overtime_minutes']
                                    ) ?>h

                                </td>

                                <td>

                                    <?= $row['meal_days'] ?? 0 ?>

                                </td>

                                <td>

                                    <?= $row['transport_days'] ?? 0 ?>

                                </td>

                                <td>

                                    <?= $row['justified_leave_hours'] ?? 0 ?>h

                                </td>

                                <td>

                                    <?= $row['vacation_hours'] ?? 0 ?>h

                                </td>

                                <td>

                                    <?= $row['sick_leave_hours'] ?? 0 ?>h

                                </td>

                                <td>

                                    <?= round(
                                        $row['unjustified_early_leave_minutes'] / 60,
                                        1
                                    ) ?>h

                                </td>

                                <td>

                                    <?= number_format(
                                        $row['paid_leave_hours'] ?? 0,
                                        1
                                    ) ?>h
                                </td>

                                <td>

                                    <?= number_format(
                                        $row['unpaid_leave_hours'] ?? 0,
                                        1
                                    ) ?>h

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