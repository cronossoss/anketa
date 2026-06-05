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
    AND presence_status IN (

    'present',

    'approved_exit',

    'business_trip',

    'remote_work'

)
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

//
// OPRAVDANI IZLAZI
//

$approvedExitToday = $conn->query("

    SELECT COUNT(*) AS total

    FROM attendance_daily_summary

    WHERE work_date = '$date'

    AND presence_status = 'approved_exit'

")->fetch_assoc()['total'];

//
// ČEKA ODOBRENJE
//

$pendingAbsences = $conn->query("

    SELECT COUNT(*) AS total

    FROM attendance_absences

    WHERE status = 'pending'

")->fetch_assoc()['total'];

$pendingExits = $conn->query("

    SELECT COUNT(*) AS total

    FROM attendance_exit_passes

    WHERE status = 'pending'

")->fetch_assoc()['total'];

$pendingRequests =
    $pendingAbsences
    +
    $pendingExits;

//
// TRENUTNO NA IZLAZNICI
//

$onExitPass = $conn->query("

    SELECT COUNT(*) AS total

    FROM attendance_exit_passes

    WHERE status = 'approved'

    AND NOW()
        BETWEEN date_from
        AND date_to

")->fetch_assoc()['total'];

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



<div class="container-fluid">

    <?php if (
        isset($_SESSION['success'])
    ): ?>

        <div
            class="alert alert-success alert-dismissible fade show"
            role="alert"
        >

            <i class="bi bi-check-circle me-2"></i>

            <?= $_SESSION['success'] ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

        <?php unset($_SESSION['success']); ?>

    <?php endif; ?>

    <div class="card border-primary shadow-sm mb-4">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                <div>

                    <h5 class="mb-1">

                        Simulation Tools

                    </h5>

                    <div class="text-muted small">

                        Generisanje test attendance podataka za poslednjih 30 dana

                    </div>

                </div>

                <div class="d-flex gap-2 flex-wrap">

                    <a
                        href="<?= url('modules/attendance/actions/generate_fake_absences.php') ?>"
                        class="btn btn-outline-primary"
                    >

                        <i class="bi bi-calendar-x me-1"></i>

                        Generiši odsustva

                    </a>

                    <a
                        href="<?= url('modules/attendance/actions/generate_fake_logs.php') ?>"
                        class="btn btn-outline-success"
                    >

                        <i class="bi bi-box-arrow-in-right me-1"></i>

                        Generiši logove

                    </a>

                    <a
                        href="<?= url('modules/attendance/actions/generate_daily_summary_all.php') ?>"
                        class="btn btn-outline-warning"
                    >

                        <i class="bi bi-bar-chart me-1"></i>

                        Generiši summary

                    </a>

                    <a
                        href="<?= url('modules/attendance/actions/reset_fake_attendance.php') ?>"
                        class="btn btn-outline-danger"
                        onclick="return confirm(
                            'Obrisati sve attendance podatke?'
                        )"
                    >

                        <i class="bi bi-trash me-1"></i>

                        Reset

                    </a>

                </div>

                <div class="row mb-4">

                    <div class="col-md-4">

                        <div class="card border-0 shadow-sm h-100">

                            <div class="card-body d-flex flex-column">

                                <div class="mb-3">

                                    <div class="fs-1 text-primary">

                                        <i class="bi bi-calendar-day"></i>

                                    </div>

                                </div>

                                <h5 class="mb-2">

                                    Dnevni izveštaj

                                </h5>

                                <div class="text-muted mb-4">

                                    Pregled prisustva, kašnjenja,
                                    izlazaka i statusa po danu.

                                </div>

                                <div class="mt-auto">

                                    <a
                                        href="<?= url('modules/attendance/reports/daily.php') ?>"
                                        class="btn btn-primary w-100"
                                    >

                                        Otvori izveštaj

                                    </a>

                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-4">

                        <div class="card border-0 shadow-sm h-100">

                            <div class="card-body d-flex flex-column">

                                <div class="mb-3">

                                    <div class="fs-1 text-success">

                                        <i class="bi bi-calendar-week"></i>

                                    </div>

                                </div>

                                <h5 class="mb-2">

                                    Nedeljni izveštaj

                                </h5>

                                <div class="text-muted mb-4">

                                    Agregirani pregled rada,
                                    kašnjenja, overtime-a i odsustava.

                                </div>

                                <div class="mt-auto">

                                    <a
                                        href="<?= url('modules/attendance/reports/weekly.php') ?>"
                                        class="btn btn-success w-100"
                                    >

                                        Otvori izveštaj

                                    </a>

                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-4">

                        <div class="card border-0 shadow-sm h-100">

                            <div class="card-body d-flex flex-column">

                                <div class="mb-3">

                                    <div class="fs-1 text-warning">

                                        <i class="bi bi-calendar-month"></i>

                                    </div>

                                </div>

                                <h5 class="mb-2">

                                    Mesečni izveštaj

                                </h5>

                                <div class="text-muted mb-4">

                                    KPI pregled attendance podataka
                                    i statistike po mesecu.

                                </div>

                                <div class="mt-auto">

                                    <a
                                        href="<?= url('modules/attendance/reports/monthly.php') ?>"
                                        class="btn btn-warning w-100"
                                    >

                                        Otvori izveštaj

                                    </a>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

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


        <div class="col-md-3 mb-4">

            <div class="card border-0 shadow-sm">

                <div class="card-body">

                    <div class="text-muted mb-2">
                        Opravdani izlazi
                    </div>

                    <h2 class="text-info">

                        <?= $approvedExitToday ?>

                    </h2>

                </div>

            </div>

        </div>

        <div class="col-md-3 mb-4">

            <div class="card border-0 shadow-sm">

                <div class="card-body">

                    <div class="text-muted mb-2">
                        Čeka odobrenje
                    </div>

                    <h2 class="text-warning">

                        <?= $pendingRequests ?>

                    </h2>

                </div>

            </div>

        </div>

        <div class="col-md-3 mb-4">

            <div class="card border-0 shadow-sm">

                <div class="card-body">

                    <div class="text-muted mb-2">
                        Na izlaznici sada
                    </div>

                    <h2 class="text-primary">

                        <?= $onExitPass ?>

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

            <table class="table table-sm table-hover align-middle">

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