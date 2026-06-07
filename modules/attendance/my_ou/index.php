<?php

require_once '../../../config/init.php';

require_login();

$pageTitle = 'Moja OJ';

$currentPage = 'my-ou-dashboard';

include "../../../layouts/admin_layout_start.php";

$userId =
    $_SESSION['user_id'];




//
// ZAPOSLENI VEZAN ZA USERA
//

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

//
// OJ KOJOM RUKOVODI
//

$stmt = $conn->prepare("

    SELECT

        id,
        code,
        name

    FROM organizational_units

    WHERE manager_employee_id = ?

    LIMIT 1

");

$stmt->bind_param(
    'i',
    $employeeId
);

$stmt->execute();

$unit =
    $stmt
    ->get_result()
    ->fetch_assoc();

if (!$unit) {

    echo '

    <div class="container-fluid">

        <div class="alert alert-warning">

            Niste definisani kao rukovodilac organizacione jedinice.

        </div>

    </div>';

    include "../../../layouts/admin_layout_end.php";

    exit;
}



//
// BROJ RADNIKA
//

$countStmt = $conn->prepare("

    SELECT COUNT(*) AS total

    FROM employees

    WHERE organizational_unit_id = ?

");

$countStmt->bind_param(
    'i',
    $unit['id']
);

$countStmt->execute();

$totalEmployees =
    $countStmt
        ->get_result()
        ->fetch_assoc()['total'];

//
// RADNICI + STATUS DANAS
//

$stmt = $conn->prepare("

    SELECT

        e.id,

        e.personal_id,

        e.first_name,
        e.last_name,

        e.position,

        ads.presence_status,

        ads.first_in,
        ads.last_out

    FROM employees e

    LEFT JOIN attendance_daily_summary ads

        ON ads.employee_id = e.id

        AND ads.work_date = CURDATE()

    WHERE e.organizational_unit_id = ?

    ORDER BY

        e.last_name,
        e.first_name

");

$stmt->bind_param(
    'i',
    $unit['id']
);

$stmt->execute();

$employees =
    $stmt->get_result();

$statusLabels = [

    'present'       => 'Prisutan',
    'late'          => 'Kašnjenje',
    'vacation'      => 'Godišnji',
    'sick_leave'    => 'Bolovanje',
    'business_trip' => 'Službeni put',
    'remote_work'   => 'Rad od kuće',
    'absent'        => 'Odsutan'

];

$statusClasses = [

    'present'       => 'bg-success',
    'late'          => 'bg-warning text-dark',
    'vacation'      => 'bg-primary',
    'sick_leave'    => 'bg-danger',
    'business_trip' => 'bg-dark',
    'remote_work'   => 'bg-secondary',
    'absent'        => 'bg-danger'

];

//
// KPI
//

$presentToday = 0;
$absentToday = 0;
$lateToday = 0;

$employees->data_seek(0);

while ($row = $employees->fetch_assoc()) {

    switch ($row['presence_status']) {

        case 'present':
            $presentToday++;
            break;

        case 'late':
            $lateToday++;
            break;

        default:
            $absentToday++;
            break;
    }
}

$employees->data_seek(0);

//
// ZAHTEVI NA ČEKANJU
//

$pendingAbsences = $conn->prepare("

    SELECT COUNT(*) total

    FROM attendance_absences aa

    JOIN employees e
        ON e.id = aa.employee_id

    WHERE e.organizational_unit_id = ?

    AND aa.status = 'pending'

");

$pendingAbsences->bind_param(
    'i',
    $unit['id']
);

$pendingAbsences->execute();

$pendingRequests =
    $pendingAbsences
        ->get_result()
        ->fetch_assoc()['total'];



?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="mb-1">

                Moja OJ

            </h3>

            <div class="text-muted">

                <?= htmlspecialchars($unit['code']) ?>

                -

                <?= htmlspecialchars($unit['name']) ?>

            </div>

        </div>

    </div>

    <div class="row mb-4">

        <div class="col-md-3">

            <div class="card border-primary shadow-sm">

                <div class="card-body">

                    <div class="text-muted">
                        Zaposleni
                    </div>

                    <div class="fs-2 fw-bold text-primary">
                        <?= $totalEmployees ?>
                    </div>

                </div>

            </div>

        </div>

        <div class="col-md-3">

            <div class="card border-success shadow-sm">

                <div class="card-body">

                    <div class="text-muted">
                        Prisutni danas
                    </div>

                    <div class="fs-2 fw-bold text-success">
                        <?= $presentToday ?>
                    </div>

                </div>

            </div>

        </div>

        <div class="col-md-3">

            <div class="card border-warning shadow-sm">

                <div class="card-body">

                    <div class="text-muted">
                        Kašnjenja
                    </div>

                    <div class="fs-2 fw-bold text-warning">
                        <?= $lateToday ?>
                    </div>

                </div>

            </div>

        </div>

        <div class="col-md-3">

            <div class="card border-danger shadow-sm">

                <div class="card-body">

                    <div class="text-muted">
                        Zahtevi na čekanju
                    </div>

                    <div class="fs-2 fw-bold text-danger">
                        <?= $pendingRequests ?>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="col-md-4">

        <div class="card shadow-sm border-0">

            <div class="card-body">

                <div class="text-muted">

                    Broj zaposlenih

                </div>

                <div class="fs-2 fw-bold text-primary">

                    <?= $totalEmployees ?>

                </div>

            </div>

        </div>

    </div>

</div>

<div class="card shadow-sm border-0">

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead>

                    <tr>

                        <th>LN</th>

                        <th>Zaposleni</th>

                        <th>Pozicija</th>

                        <th>Dolazak</th>

                        <th>Odlazak</th>

                        <th>Status</th>

                    </tr>

                </thead>

                <tbody>

                    <?php while ($row = $employees->fetch_assoc()): ?>

                        <?php

                        $status =
                            $row['presence_status']
                            ?? 'absent';

                        ?>

                        <tr>

                            <td>

                                <?= htmlspecialchars(
                                    $row['personal_id']
                                ) ?>

                            </td>

                            <td>

                                <strong>

                                    <?= htmlspecialchars(

                                        $row['last_name']
                                            . ' ' .
                                            $row['first_name']

                                    ) ?>

                                </strong>

                            </td>

                            <td>

                                <?= htmlspecialchars(
                                    $row['position']
                                ) ?>

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

                                <span class="badge <?= $statusClasses[$status] ?? 'bg-secondary' ?>">

                                    <?= htmlspecialchars(

                                        $statusLabels[$status]
                                            ?? $status

                                    ) ?>

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

<?php include "../../../layouts/admin_layout_end.php"; ?>