<?php

require_once ROOT_PATH . '/helpers/organization_tree.php';

require_once ROOT_PATH . '/helpers/manager_dashboard.php';

$managerEmployeeId =
    (int)$_SESSION['employee_id'];

$employeeCount =
    getManagedEmployeeCount(
        $conn,
        $managerEmployeeId
    );

$pendingAbsences =
    getPendingAbsenceCount(
        $conn,
        $managerEmployeeId
    );

$pendingExitPasses =
    getPendingExitPassCount(
        $conn,
        $managerEmployeeId
    );

$dangerAnnouncements =
    (int)$conn
        ->query("
            SELECT COUNT(*) total
            FROM announcements
            WHERE active = 1
              AND priority = 'danger'
        ")
        ->fetch_assoc()['total'];
$managedEmployees =
    getManagedEmployeeIds(
        $conn,
        $managerEmployeeId
    );

$presentToday = 0;
$onLeaveToday = 0;
$onExitToday = 0;
$notRecordedToday = 0;
$notRecordedEmployees = [];

if (!empty($managedEmployees)) {

    $employeeIds =
        implode(
            ',',
            array_map(
                'intval',
                $managedEmployees
            )
        );

    /*
    |--------------------------------------------------------------------------
    | ODSUSTVA DANAS
    |--------------------------------------------------------------------------
    */

    $result = $conn->query("
        SELECT COUNT(DISTINCT employee_id) total
        FROM attendance_absences
        WHERE status = 'approved'
          AND CURDATE()
              BETWEEN DATE(date_from)
              AND DATE(date_to)
          AND employee_id IN ($employeeIds)
    ");

    $onLeaveToday =
        (int)$result
            ->fetch_assoc()['total'];

    /*
    |--------------------------------------------------------------------------
    | IZLAZNICE SADA
    |--------------------------------------------------------------------------
    */

    $result = $conn->query("
        SELECT COUNT(DISTINCT employee_id) total
        FROM attendance_exit_passes
        WHERE status = 'approved'
          AND NOW()
              BETWEEN date_from
              AND date_to
          AND employee_id IN ($employeeIds)
    ");

    $onExitToday =
        (int)$result
            ->fetch_assoc()['total'];

    /*
    |--------------------------------------------------------------------------
    | EVIDENTIRANI DANAS
    |--------------------------------------------------------------------------
    */

    $result = $conn->query("
        SELECT COUNT(DISTINCT employee_id) total
        FROM attendance_logs
        WHERE DATE(access_datetime) = CURDATE()
        AND employee_id IN ($employeeIds)
    ");

    $recordedToday =
        (int)$result
            ->fetch_assoc()['total'];

    $result = $conn->query("
        SELECT
            e.id,
            e.first_name,
            e.last_name,
            e.personal_id
        FROM employees e

        WHERE e.id IN ($employeeIds)

        AND e.id NOT IN (

                SELECT DISTINCT employee_id
                FROM attendance_logs
                WHERE DATE(access_datetime) = CURDATE()

        )

        AND e.id NOT IN (

                SELECT DISTINCT employee_id
                FROM attendance_absences
                WHERE status = 'approved'
                AND CURDATE()
                    BETWEEN DATE(date_from)
                    AND DATE(date_to)

        )

        ORDER BY
            e.last_name,
            e.first_name
    ");

    $notRecordedEmployees =
        $result->fetch_all(MYSQLI_ASSOC);

    /*
    |--------------------------------------------------------------------------
    | PRISUTNI
    |--------------------------------------------------------------------------
    */

    $notRecordedToday =
        $employeeCount
        - $recordedToday
        - $onLeaveToday;

    if ($notRecordedToday < 0) {
        $notRecordedToday = 0;
    }

    $presentToday =
        $employeeCount
        - $onLeaveToday
        - $onExitToday
        - $notRecordedToday;

    if ($presentToday < 0) {
        $presentToday = 0;
    }

    if ($presentToday < 0) {
        $presentToday = 0;
    }
}

$pendingRequests = [];

if (!empty($managedEmployees)) {

    $employeeIds =
        implode(
            ',',
            array_map(
                'intval',
                $managedEmployees
            )
        );

    $sql = "

        SELECT
            a.id,
            'absence' AS request_type,
            e.first_name,
            e.last_name,
            t.name AS type_name,
            a.date_from,
            a.date_to,
            a.created_at

        FROM attendance_absences a

        INNER JOIN employees e
            ON e.id = a.employee_id

        INNER JOIN attendance_absence_types t
            ON t.id = a.absence_type_id

        WHERE a.status = 'pending'
          AND a.employee_id IN ($employeeIds)

        UNION ALL

        SELECT
            p.id,
            'exit' AS request_type,
            e.first_name,
            e.last_name,
            t.name AS type_name,
            p.date_from,
            p.date_to,
            p.created_at

        FROM attendance_exit_passes p

        INNER JOIN employees e
            ON e.id = p.employee_id

        INNER JOIN attendance_exit_types t
            ON t.id = p.exit_type_id

        WHERE p.status = 'pending'
          AND p.employee_id IN ($employeeIds)

        ORDER BY created_at DESC

        LIMIT 5
    ";

    $pendingRequests =
        $conn
        ->query($sql)
        ->fetch_all(MYSQLI_ASSOC);
}
?>

<div class="row g-3 mb-4">

    <div class="col-md-3">

        <a
            href="<?= url('modules/attendance/approval_requests/index.php') ?>"
            class="text-decoration-none text-reset">

            <div class="dashboard-card">

                <div class="dashboard-card-title">

                    Zahtevi

                </div>

                <div class="dashboard-card-value">

                    <?= $pendingAbsences + $pendingExitPasses ?>

                </div>

            </div>

        </a>

    </div>

    <div class="col-md-3">

        <div class="dashboard-card">

            <div class="dashboard-card-title">

                Odsustva

            </div>

            <div class="dashboard-card-value">

                <?= $pendingAbsences ?>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="dashboard-card">

            <div class="dashboard-card-title">

                Izlaznice

            </div>

            <div class="dashboard-card-value">

                <?= $pendingExitPasses ?>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="dashboard-card">

            <div class="dashboard-card-title">

                Obaveštenja

            </div>

            <div class="dashboard-card-value">

                <?= $dangerAnnouncements ?>

            </div>

        </div>

    </div>

</div>

<div class="card shadow-sm mb-4">

    <div class="card-header">

        <strong>

            Moja organizaciona jedinica

        </strong>

    </div>

    <div class="card-body">

        <h4>

            <?= e($_SESSION['department_name'] ?? '') ?>

        </h4>

        <div class="row text-center">

            <div class="col-md-2">

                <div class="text-muted small">
                    Zaposlenih
                </div>

                <div class="fs-3 fw-bold">
                    <?= $employeeCount ?>
                </div>

            </div>

            <div class="col-md-2">

                <div class="text-muted small">
                    Prisutnih
                </div>

                <div class="fs-3 fw-bold text-success">
                    <?= $presentToday ?>
                </div>

            </div>

            <div class="col-md-2">

                <div class="text-muted small">
                    Odsustvo
                </div>

                <div class="fs-3 fw-bold text-warning">
                    <?= $onLeaveToday ?>
                </div>

            </div>

            <div class="col-md-2">

                <div class="text-muted small">
                    Izlaznica
                </div>

                <div class="fs-3 fw-bold text-info">
                    <?= $onExitToday ?>
                </div>

            </div>

            <div class="col-md-2">

                <div class="text-muted small">
                    Neevidentirani
                </div>

                <div class="fs-3 fw-bold text-danger">
                    <?= $notRecordedToday ?>
                </div>

            </div>

        </div>

    </div>

</div>

<div class="card shadow-sm mb-4">

    <div class="card-header">

        <strong>
            Neevidentirani danas
        </strong>

    </div>

    <div class="card-body">

        <?php if (empty($notRecordedEmployees)): ?>

            <div class="text-success">

                Nema neevidentiranih zaposlenih.

            </div>

        <?php else: ?>

            <div class="list-group">

                <?php foreach ($notRecordedEmployees as $employee): ?>

                    <div class="list-group-item d-flex justify-content-between align-items-center">

                        <div>

                            <strong>

                                <?= e(
                                    $employee['first_name']
                                        . ' '
                                        . $employee['last_name']
                                ) ?>

                            </strong>

                            <div class="small text-muted">

                                <?= e(
                                    $employee['personal_id']
                                ) ?>

                            </div>

                        </div>

                        <button
                            class="btn btn-warning btn-sm resolve-status-btn"
                            data-employee-id="<?= $employee['id'] ?>"
                            data-employee-name="<?= e(
                                $employee['first_name'] . ' ' .
                                $employee['last_name']
                            ) ?>">
                            Otvori slučaj
                        </button>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>

</div>

<?php

$followUpCases = [];

if (!empty($managedEmployees)) {

    $employeeIds =
        implode(
            ',',
            array_map(
                'intval',
                $managedEmployees
            )
        );

    $followUpCases =
        $conn->query("
            SELECT

                a.id,

                e.first_name,
                e.last_name,

                t.name AS absence_type,

                a.date_from

            FROM attendance_absences a

            INNER JOIN employees e
                ON e.id = a.employee_id

            INNER JOIN attendance_absence_types t
                ON t.id = a.absence_type_id

            WHERE a.employee_id IN ($employeeIds)

                AND a.status = 'approved'

                AND a.follow_up_required = 1

                AND a.closed_at IS NULL

                            ORDER BY
                                a.date_from DESC

                            LIMIT 10
                        ")->fetch_all(MYSQLI_ASSOC);
                }

?>

<div class="card shadow-sm mb-4">

    <div class="card-header">

        <strong>

            Otvoreni slučajevi

        </strong>

    </div>

    <div class="card-body">

        <?php if (empty($followUpCases)): ?>

            <div class="text-success">

                Nema otvorenih slučajeva.

            </div>

        <?php else: ?>

            <div class="table-responsive">

                <table class="table table-sm align-middle">

                    <thead>

                        <tr>

                            <th>Zaposleni</th>

                            <th>Status</th>

                            <th>Od datuma</th>

                            <th>Akcija</th>

                            
                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($followUpCases as $case): ?>

                            <tr>

                                <td>
                                    <?= e(
                                        $case['first_name']
                                        . ' '
                                        . $case['last_name']
                                    ) ?>
                                </td>

                                <td>
                                    <?= e(
                                        $case['absence_type']
                                    ) ?>
                                </td>

                                <td>
                                    <?= date(
                                        'd.m.Y',
                                        strtotime(
                                            $case['date_from']
                                        )
                                    ) ?>
                                </td>

                                <td>

                                    <a
                                        href="<?= url(
                                            'modules/attendance/corrections/view.php?id='
                                            . $case['id']
                                        ) ?>"
                                        class="btn btn-sm btn-primary">

                                        Pregled

                                    </a>

                                </td>

                            </tr>

                            <?php endforeach; ?>   

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    </div>

</div>

<div class="card shadow-sm">

    <div class="card-header d-flex justify-content-between">

        <strong>
            Zahtevi za odobrenje
        </strong>

        <a href="<?= url('modules/attendance/approval_requests/index.php') ?>">
            Prikaži sve
        </a>

    </div>

    <div class="card-body p-0">

        <?php if (empty($pendingRequests)): ?>

            <div class="p-3 text-muted">

                Nema zahteva za odobrenje.

            </div>

        <?php else: ?>

            <div class="table-responsive">

                <table class="table mb-0">

                    <thead>

                        <tr>

                            <th>Zaposleni</th>

                            <th>Tip</th>

                            <th>Period</th>

                            <th>Podneto</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($pendingRequests as $request): ?>

                            <tr>

                                <td>

                                    <?= e(
                                        $request['first_name']
                                            . ' '
                                            . $request['last_name']
                                    ) ?>

                                </td>

                                <td>

                                    <?= e(
                                        $request['type_name']
                                    ) ?>

                                </td>

                                <td>

                                    <?= e(
                                        date(
                                            'd.m.Y',
                                            strtotime(
                                                $request['date_from']
                                            )
                                        )
                                    ) ?>

                                    -

                                    <?= e(
                                        date(
                                            'd.m.Y',
                                            strtotime(
                                                $request['date_to']
                                            )
                                        )
                                    ) ?>

                                </td>

                                <td>

                                    <?= e(
                                        date(
                                            'd.m.Y H:i',
                                            strtotime(
                                                $request['created_at']
                                            )
                                        )
                                    ) ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    </div>

</div>

<div
    class="modal fade"
    id="resolveStatusModal"
    tabindex="-1">

    <div class="modal-dialog">

        <div class="modal-content">

            <form
                method="POST"
                action="<?= url(
                    'modules/attendance/actions/create_case.php'
                ) ?>">

                <input
                    type="hidden"
                    name="csrf"
                    value="<?= csrf_token() ?>">

                <input
                    type="hidden"
                    name="employee_id"
                    id="resolveEmployeeId">

                <div class="modal-header">

                    <h5 class="modal-title">

                        Otvori slučaj

                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    <p
                        id="resolveEmployeeText"
                        class="fw-semibold">
                    </p>

                    <div class="mb-3">

                        <label class="form-label">

                            Status zaposlenog

                        </label>

                        <select
                            name="case_type"
                            class="form-select"
                            required>

                            <option value="missing_attendance">
                                Neevidentiran dolazak
                            </option>

                            <option value="doctor">
                                Odlazak kod lekara
                            </option>

                            <option value="business_trip">
                                Službeni put
                            </option>

                            <option value="private_exit">
                                Privatni izlazak
                            </option>

                            <option value="official_exit">
                                Službeni izlazak
                            </option>

                            <option value="tardiness">
                                Kašnjenje
                            </option>

                        </select>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">

                            Napomena

                        </label>

                        <textarea
                            name="description"
                            class="form-control"
                            rows="4"
                            required></textarea>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                        Otkaži

                    </button>

                    <button
                        type="submit"
                        class="btn btn-warning">

                        Otvori slučaj

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<script>

document
    .querySelectorAll(
        '.resolve-status-btn'
    )
    .forEach(btn => {

        btn.addEventListener(
            'click',
            () => {

                document
                    .getElementById(
                        'resolveEmployeeId'
                    )
                    .value =
                    btn.dataset.employeeId;

                document
                    .getElementById(
                        'resolveEmployeeText'
                    )
                    .innerText =
                    'Zaposleni: ' +
                    btn.dataset.employeeName;

                new bootstrap.Modal(
                    document.getElementById(
                        'resolveStatusModal'
                    )
                ).show();
            }
        );
    });

</script>