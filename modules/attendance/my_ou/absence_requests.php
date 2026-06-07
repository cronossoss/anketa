<?php

require_once '../../../config/init.php';

require_login();

$pageTitle = 'Zahtevi za odsustva';

$currentPage = 'my-ou-absence-requests';

include "../../../layouts/admin_layout_start.php";

$userId =
    $_SESSION['user_id'];

//
// EMPLOYEE
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
// OJ
//

$stmt = $conn->prepare("

    SELECT id, code, name

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

    $_SESSION['error'] =
        'Niste definisani kao rukovodilac OJ.';

    header(
        'Location: index.php'
    );

    exit;
}

//
// ZAHTEVI
//

$stmt = $conn->prepare("

    SELECT

        aa.id,

        aa.date_from,
        aa.date_to,

        aa.created_at,

        aat.name AS absence_type,

        e.first_name,
        e.last_name

    FROM attendance_absences aa

    JOIN employees e
        ON e.id = aa.employee_id

    JOIN attendance_absence_types aat
        ON aat.id = aa.absence_type_id

    WHERE e.organizational_unit_id = ?

    AND aa.status = 'pending'

    ORDER BY aa.created_at ASC

");

$stmt->bind_param(
    'i',
    $unit['id']
);

$stmt->execute();

$requests =
    $stmt->get_result();

?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="mb-1">

                Zahtevi za odsustva

            </h3>

            <div class="text-muted">

                <?= htmlspecialchars($unit['code']) ?>

                -

                <?= htmlspecialchars($unit['name']) ?>

            </div>

        </div>

        <a
            href="index.php"
            class="btn btn-outline-secondary">

            Nazad

        </a>

    </div>

    <div class="card shadow-sm border-0">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead>

                        <tr>

                            <th>Zaposleni</th>

                            <th>Vrsta odsustva</th>

                            <th>Od</th>

                            <th>Do</th>

                            <th class="text-end">

                                Akcije

                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php if ($requests->num_rows === 0): ?>

                            <tr>

                                <td
                                    colspan="5"
                                    class="text-center text-muted py-4">

                                    Nema zahteva za odobravanje.

                                </td>

                            </tr>

                        <?php endif; ?>

                        <?php while ($row = $requests->fetch_assoc()): ?>

                            <tr>

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
                                        $row['absence_type']
                                    ) ?>

                                </td>

                                <td>

                                    <?= date(
                                        'd.m.Y H:i',
                                        strtotime(
                                            $row['date_from']
                                        )
                                    ) ?>

                                </td>

                                <td>

                                    <?= date(
                                        'd.m.Y H:i',
                                        strtotime(
                                            $row['date_to']
                                        )
                                    ) ?>

                                </td>

                                <td class="text-end">

                                    <div class="btn-group btn-group-sm">

                                        <a
                                            href="approve_absence.php?id=<?= $row['id'] ?>"
                                            class="btn btn-success">

                                            <i class="bi bi-check-lg"></i>

                                        </a>

                                        <a
                                            href="reject_absence.php?id=<?= $row['id'] ?>"
                                            class="btn btn-danger">

                                            <i class="bi bi-x-lg"></i>

                                        </a>

                                    </div>

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