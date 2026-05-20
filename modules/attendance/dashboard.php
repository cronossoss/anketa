<?php

require_once '../../config/init.php';

$currentPage = 'attendance-dashboard';

$pageTitle = "Attendance Dashboard";

include "../../layouts/admin_layout_start.php";

$date = date('Y-m-d');

//
// TRENUTNO PRISUTNI
//

$presentNow = $conn->query("
    SELECT COUNT(*) AS total

    FROM employees e

    WHERE (
        SELECT al.direction
        FROM attendance_logs al
        WHERE al.employee_id = e.id
        ORDER BY al.access_datetime DESC
        LIMIT 1
    ) = 'IN'
")->fetch_assoc()['total'];

//
// DANAS PRISUTNI
//

$presentToday = $conn->query("
    SELECT COUNT(*) AS total

    FROM attendance_daily_summary

    WHERE work_date = '$date'
    AND presence_status = 'present'
")->fetch_assoc()['total'];

//
// DANAS ODSUTNI
//

$absentToday = $conn->query("
    SELECT COUNT(*) AS total

    FROM attendance_daily_summary

    WHERE work_date = '$date'
    AND presence_status = 'absent'
")->fetch_assoc()['total'];

//
// KASNI DANAS
//

$lateToday = $conn->query("
    SELECT COUNT(*) AS total

    FROM attendance_daily_summary

    WHERE work_date = '$date'
    AND late_minutes > 0
")->fetch_assoc()['total'];

//
// PROSEČNO RADNO VREME
//

$avgWorked = $conn->query("
    SELECT AVG(worked_minutes) AS avg_minutes

    FROM attendance_daily_summary

    WHERE work_date = '$date'
    AND worked_minutes > 0
")->fetch_assoc()['avg_minutes'];

$avgWorkedHours =
    round($avgWorked / 60, 1);

//
// PRISUSTVO PO ORGANIZACIONIM JEDINICAMA
//

$units = $conn->query("

    SELECT

        ou.name AS organizational_unit,

        COUNT(*) AS total_present

    FROM attendance_daily_summary ads

    JOIN employees e
        ON e.id = ads.employee_id

    LEFT JOIN organizational_units ou
        ON ou.id = e.organizational_unit_id

    WHERE ads.work_date = '$date'
    AND ads.presence_status = 'present'

    GROUP BY ou.id

    ORDER BY total_present DESC

");

$earlyLeaveToday = $conn->query("

    SELECT COUNT(*) AS total

    FROM attendance_daily_summary

    WHERE work_date = CURDATE()

    AND early_leave_minutes > 0

")->fetch_assoc()['total'];

$earlyLeaveEmployees = $conn->query("

    SELECT

        ads.*,

        e.first_name,
        e.last_name,
        e.personal_id

    FROM attendance_daily_summary ads

    JOIN employees e
        ON e.id = ads.employee_id

    WHERE ads.work_date = CURDATE()

    AND ads.early_leave_minutes > 0

    ORDER BY ads.early_leave_minutes DESC

");

?>



<div class="container-fluid">

    <div class="d-flex gap-2 mb-4 flex-wrap">

        <a href="actions/generate_fake_logs.php"
            class="btn btn-primary">

            Generiši logove

        </a>

        <a href="actions/delete_fake_logs.php"
            class="btn btn-danger">

            Obriši logove

        </a>

        <a href="actions/generate_daily_summary.php"
            class="btn btn-success">

            Generiši summary

        </a>

        <a href="actions/delete_daily_summary.php"
            class="btn btn-outline-danger">

            Obriši summary

        </a>

        <a href="actions/reset_and_generate.php"
            class="btn btn-dark">

            Resetuj i generiši sve

        </a>

    </div>

    <div class="row">

        <!-- TRENUTNO PRISUTNI -->

        <div class="col-md-3 mb-4">

            <div class="card border-0 shadow-sm">

                <div class="card-body">

                    <div class="text-muted mb-2">
                        Trenutno prisutni
                    </div>

                    <h2>
                        <?= $presentNow ?>
                    </h2>

                </div>

            </div>

        </div>

        <!-- DANAS PRISUTNI -->

        <div class="col-md-3 mb-4">

            <div class="card border-0 shadow-sm">

                <div class="card-body">

                    <div class="text-muted mb-2">
                        Danas prisutni
                    </div>

                    <h2>
                        <?= $presentToday ?>
                    </h2>

                </div>

            </div>

        </div>

        <!-- DANAS ODSUTNI -->

        <div class="col-md-3 mb-4">

            <div class="card border-0 shadow-sm">

                <div class="card-body">

                    <div class="text-muted mb-2">
                        Danas odsutni
                    </div>

                    <h2>
                        <?= $absentToday ?>
                    </h2>

                </div>

            </div>

        </div>

        <!-- KASNI DANAS -->

        <div class="col-md-3 mb-4">

            <div class="card border-0 shadow-sm">

                <div class="card-body">

                    <div class="text-muted mb-2">
                        Kasni danas
                    </div>

                    <h2 class="text-warning">
                        <?= $lateToday ?>
                    </h2>

                </div>

            </div>

        </div>

        <!-- RANIJE IZLAZ DANAS -->

        <div class="col-md-3 mb-4">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body">

                    <div class="text-muted mb-2">

                        Raniji izlazak

                    </div>

                    <div class="fs-1 fw-bold text-danger">

                        <?= $earlyLeaveToday ?>

                    </div>

                </div>

            </div>

        </div>

        <!-- PROSEČNO RADNO VREME -->

        <div class="col-md-3 mb-4">

            <div class="card border-0 shadow-sm">

                <div class="card-body">

                    <div class="text-muted mb-2">
                        Prosečno radno vreme
                    </div>

                    <h2>
                        <?= $avgWorkedHours ?> h
                    </h2>

                </div>

            </div>

        </div>

    </div>

    <div class="row">

        <div class="col-lg-6 mb-4">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <h5 class="mb-4">

                        Prisustvo po organizacionim jedinicama

                    </h5>

                    <table class="table table-hover align-middle">

                        <thead>

                            <tr>

                                <th>Organizaciona jedinica</th>

                                <th>Prisutni</th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php while ($unit = $units->fetch_assoc()): ?>

                                <tr>

                                    <td>

                                        <?= htmlspecialchars(
                                            $unit['organizational_unit']
                                                ?? 'Nedefinisano'
                                        ) ?>

                                    </td>

                                    <td>

                                        <span class="badge bg-primary">

                                            <?= $unit['total_present'] ?>

                                        </span>

                                    </td>

                                </tr>

                            <?php endwhile; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

    <?php

    $lateEmployees = $conn->query("
    SELECT
        e.first_name,
        e.last_name,
        ads.first_in,
        ads.late_minutes

    FROM attendance_daily_summary ads

    JOIN employees e
        ON e.id = ads.employee_id

    WHERE ads.work_date = '$date'
    AND ads.late_minutes > 0

    ORDER BY ads.late_minutes DESC
");

    ?>

    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <h5 class="mb-4">
                Zaposleni koji kasne
            </h5>

            <table class="table table-hover">

                <thead>
                    <tr>
                        <th>Zaposleni</th>
                        <th>Dolazak</th>
                        <th>Kašnjenje</th>
                    </tr>
                </thead>

                <tbody>

                    <?php while ($row = $lateEmployees->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?= htmlspecialchars(
                                    $row['first_name']
                                        . ' ' .
                                        $row['last_name']
                                ) ?>
                            </td>

                            <td>
                                <?= date(
                                    'H:i',
                                    strtotime($row['first_in'])
                                ) ?>
                            </td>

                            <td>

                                <span class="badge bg-warning text-dark">

                                    <?= $row['late_minutes'] ?>
                                    min

                                </span>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    </div>

    <div class="card shadow-sm border-0">

        <div class="card-body">

            <h5 class="mb-4">

                Radnici sa ranijim izlazom

            </h5>

            <table class="table table-hover align-middle">

                <thead>

                    <tr>

                        <th>Zaposleni</th>

                        <th>Izlaz</th>

                        <th>Raniji izlazak</th>

                    </tr>

                </thead>

                <tbody>

                    <?php while ($row = $earlyLeaveEmployees->fetch_assoc()): ?>

                        <tr>

                            <td>

                                <?= htmlspecialchars(

                                    $row['personal_id']
                                        . ' - '
                                        . $row['last_name']
                                        . ' '
                                        . $row['first_name']

                                ) ?>

                            </td>

                            <td>

                                <?= date(
                                    'H:i',
                                    strtotime($row['last_out'])
                                ) ?>

                            </td>

                            <td>

                                <span class="badge bg-danger">

                                    <?= $row['early_leave_minutes'] ?>
                                    min

                                </span>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<?php include "../../layouts/admin_layout_end.php"; ?>