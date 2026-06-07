<?php

require_once '../../../config/init.php';
require_once '../../../helpers/format.php';

require_login();

$pageTitle = 'Moji zahtevi';

$currentPage = 'attendance-requests';

include "../../../layouts/layout_start.php";

$userId = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT employee_id
    FROM users
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param('i', $userId);
$stmt->execute();

$user = $stmt
    ->get_result()
    ->fetch_assoc();

$employeeId = (int)$user['employee_id'];

$requests = [];

$result = $conn->query("

    SELECT

        aa.id,
        aa.created_at,

        'absence' AS request_type,

        aat.name AS request_name,

        aa.date_from,
        aa.date_to,

        aa.status,
        aa.rejection_reason

    FROM attendance_absences aa

    JOIN attendance_absence_types aat
        ON aat.id = aa.absence_type_id

    WHERE aa.employee_id = {$employeeId}

");

while ($row = $result->fetch_assoc()) {

    $requests[] = $row;
}

$result = $conn->query("

    SELECT

        ep.id,
        ep.created_at,

        'exit' AS request_type,

        et.name AS request_name,

        ep.date_from,
        ep.date_to,

        ep.status,
        ep.rejection_reason

    FROM attendance_exit_passes ep

    JOIN attendance_exit_types et
        ON et.id = ep.exit_type_id

    WHERE ep.employee_id = {$employeeId}

");

while ($row = $result->fetch_assoc()) {

    $requests[] = $row;
}

usort(
    $requests,
    function ($a, $b) {

        return strtotime($b['created_at'])
            <=> strtotime($a['created_at']);
    }
);

?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="mb-1">
                Moji zahtevi
            </h3>

            <div class="text-muted">
                Pregled svih zahteva
            </div>

        </div>

        <a
            href="create.php"
            class="btn btn-primary">

            <i class="bi bi-plus-lg me-1"></i>

            Novi zahtev

        </a>

    </div>

    <?php if (!empty($_SESSION['success'])): ?>

        <div class="alert alert-success">

            <?= htmlspecialchars($_SESSION['success']) ?>

        </div>

        <?php unset($_SESSION['success']); ?>

    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>

        <div class="alert alert-danger">

            <?= htmlspecialchars($_SESSION['error']) ?>

        </div>

        <?php unset($_SESSION['error']); ?>

    <?php endif; ?>

    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead>

                        <tr>

                            <th>Vrsta</th>

                            <th>Tip</th>

                            <th>Period</th>

                            <th>Status</th>

                            <th>Razlog</th>

                            <th>Kreirano</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($requests as $row): ?>

                            <tr>

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

                                    <?php

                                    $class = 'bg-secondary';
                                    $label = $row['status'];

                                    if ($row['status'] === 'pending') {
                                        $class = 'bg-warning text-dark';
                                        $label = 'Na čekanju';
                                    }

                                    if ($row['status'] === 'approved') {
                                        $class = 'bg-success';
                                        $label = 'Odobreno';
                                    }

                                    if ($row['status'] === 'rejected') {
                                        $class = 'bg-danger';
                                        $label = 'Odbijeno';
                                    }

                                    ?>

                                    <span class="badge <?= $class ?>">

                                        <?= $label ?>

                                    </span>

                                </td>

                                <td>

                                    <?php if (
                                        $row['status'] === 'rejected'
                                        &&
                                        !empty($row['rejection_reason'])
                                    ): ?>

                                        <button
                                            class="btn btn-sm btn-outline-danger"
                                            data-bs-toggle="tooltip"
                                            title="<?= htmlspecialchars($row['rejection_reason']) ?>">

                                            Razlog

                                        </button>

                                    <?php else: ?>

                                        -

                                    <?php endif; ?>

                                </td>

                                <td>

                                    <?= sr_datetime(
                                        $row['created_at']
                                    ) ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

<?php include "../../../layouts/layout_end.php"; ?>