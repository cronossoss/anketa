<?php

require_once '../../config/init.php';

require_login();

$pageTitle = 'Trenutno stanje';

$currentPage = 'attendance-current-status';

include "../../layouts/admin_layout_start.php";

$referenceDate =

    !empty($_GET['reference'])

    ? date(
        'Y-m-d H:i:s',
        strtotime($_GET['reference'])
    )

    : date('Y-m-d H:i:s');

$employees = $conn->query("

    SELECT

        e.id,

        e.first_name,
        e.last_name,

        ou.code AS organizational_unit

    FROM employees e

    LEFT JOIN organizational_units ou
        ON ou.id = e.organizational_unit_id

    ORDER BY
        e.last_name,
        e.first_name

");

$rows = [];

$inCompany = 0;
$businessExit = 0;
$privateExit = 0;
$absent = 0;

while ($employee = $employees->fetch_assoc()) {

    $employeeId =
        $employee['id'];

    //
    // AKTIVNA IZLAZNICA
    //

    $exitQuery = $conn->prepare("

        SELECT

            et.name,
            et.code,

            ep.date_from

        FROM attendance_exit_passes ep

        JOIN attendance_exit_types et
            ON et.id = ep.exit_type_id

        WHERE ep.employee_id = ?

        AND ep.status = 'approved'

        AND ? BETWEEN ep.date_from
          AND ep.date_to

        LIMIT 1

    ");

    $exitQuery->bind_param(
        'is',
        $employeeId,
        $referenceDate
    );

    $exitQuery->execute();

    $activeExit =
        $exitQuery
        ->get_result()
        ->fetch_assoc();

    //
    // POSLEDNJI LOG
    //

    $logQuery = $conn->prepare("

    SELECT

        direction,
        access_datetime

    FROM attendance_logs

    WHERE employee_id = ?

    AND access_datetime <= ?

    ORDER BY access_datetime DESC

    LIMIT 1

");

    $logQuery->bind_param(
        'is',
        $employeeId,
        $referenceDate
    );

    $logQuery->execute();

    $lastLog =
        $logQuery
        ->get_result()
        ->fetch_assoc();

    $absenceQuery = $conn->prepare("

    SELECT

        aat.name,
        aat.code

    FROM attendance_absences aa

    JOIN attendance_absence_types aat
        ON aat.id = aa.absence_type_id

    WHERE aa.employee_id = ?

    AND aa.status = 'approved'

    AND DATE(?)
        BETWEEN DATE(aa.date_from)
        AND DATE(aa.date_to)

    LIMIT 1

");

    $absenceQuery->bind_param(
        'is',
        $employeeId,
        $referenceDate
    );

    $absenceQuery->execute();

    $absence =
        $absenceQuery
        ->get_result()
        ->fetch_assoc();

    $status = 'Odsutan';
    $badge = 'bg-danger';
    $since = '-';

    if ($absence) {

        $status =
            $absence['name'];

        $badge =
            'bg-secondary';

        $absent++;
    } elseif ($activeExit) {

        if (
            strtoupper(
                $activeExit['code']
            ) === 'SLUZ'
        ) {

            $status =
                'Službeni izlazak';

            $badge =
                'bg-primary';

            $businessExit++;
        } else {

            $status =
                $activeExit['name'];

            $badge =
                'bg-warning text-dark';

            $privateExit++;
        }

        $since = date(
            'H:i',
            strtotime(
                $activeExit['date_from']
            )
        );
    } elseif (

        $lastLog
        &&
        $lastLog['direction'] === 'IN'

    ) {

        $status =
            'U firmi';

        $badge =
            'bg-success';

        $inCompany++;

        $since = date(
            'H:i',
            strtotime(
                $lastLog['access_datetime']
            )
        );
    } else {

        $absent++;
    }

    $rows[] = [

        'employee' =>
        $employee['last_name']
            . ' '
            . $employee['first_name'],

        'ou' =>
        $employee['organizational_unit'],

        'status' =>
        $status,

        'badge' =>
        $badge,

        'since' =>
        $since

    ];
}

?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <form method="GET" class="row g-2 mb-4">

            <div class="col-md-4">

                <input
                    type="datetime-local"
                    name="reference"
                    class="form-control"
                    value="<?= htmlspecialchars(
                                $_GET['reference']
                                    ?? date('Y-m-d\TH:i')
                            ) ?>">

            </div>

            <div class="col-md-auto">

                <button
                    type="submit"
                    class="btn btn-primary">
                    Prikaži
                </button>

            </div>

        </form>

        <div>

            <h3 class="mb-1">

                Trenutno stanje

            </h3>

            <div class="text-muted">

                Pregled prisustva zaposlenih u realnom vremenu

            </div>

        </div>

    </div>

    <div class="row mb-4">

        <div class="col-md-3">

            <div class="card shadow-sm border-success">

                <div class="card-body">

                    <div class="text-muted">

                        U firmi

                    </div>

                    <div class="fs-2 fw-bold text-success">

                        <?= $inCompany ?>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-md-3">

            <div class="card shadow-sm border-primary">

                <div class="card-body">

                    <div class="text-muted">

                        Službeni izlaz

                    </div>

                    <div class="fs-2 fw-bold text-primary">

                        <?= $businessExit ?>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-md-3">

            <div class="card shadow-sm border-warning">

                <div class="card-body">

                    <div class="text-muted">

                        Privatni izlaz

                    </div>

                    <div class="fs-2 fw-bold text-warning">

                        <?= $privateExit ?>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-md-3">

            <div class="card shadow-sm border-danger">

                <div class="card-body">

                    <div class="text-muted">

                        Odsutni

                    </div>

                    <div class="fs-2 fw-bold text-danger">

                        <?= $absent ?>

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

                            <th>Zaposleni</th>
                            <th>OJ</th>
                            <th>Status</th>
                            <th>Od</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($rows as $row): ?>

                            <tr>

                                <td>

                                    <?= htmlspecialchars(
                                        $row['employee']
                                    ) ?>

                                </td>

                                <td>

                                    <?= htmlspecialchars(
                                        $row['ou'] ?? '-'
                                    ) ?>

                                </td>

                                <td>

                                    <span class="badge <?= $row['badge'] ?>">

                                        <?= htmlspecialchars(
                                            $row['status']
                                        ) ?>

                                    </span>

                                </td>

                                <td>

                                    <?= $row['since'] ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

<?php if (empty($_GET['reference'])): ?>
    <script>
        setTimeout(() => {
            location.reload();
        }, 30000);
    </script>
<?php endif; ?>

<?php include "../../layouts/admin_layout_end.php"; ?>