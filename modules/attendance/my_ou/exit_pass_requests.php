<?php

require_once '../../../config/init.php';

require_login();

$pageTitle = 'Zahtevi za izlaznice';

$currentPage = 'my-ou-exit-pass-requests';

include "../../../layouts/layout_start.php";

$userId =
    $_SESSION['user_id'];

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

    $_SESSION['error'] =
        'Niste definisani kao rukovodilac OJ.';

    header(
        'Location: index.php'
    );

    exit;
}

$stmt = $conn->prepare("

    SELECT

        ep.id,

        ep.date_from,
        ep.date_to,

        ep.note,

        et.name AS exit_type,

        e.first_name,
        e.last_name

    FROM attendance_exit_passes ep

    JOIN employees e
        ON e.id = ep.employee_id

    JOIN attendance_exit_types et
        ON et.id = ep.exit_type_id

    WHERE e.organizational_unit_id = ?

    AND ep.status = 'pending'

    ORDER BY ep.created_at ASC

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

                Zahtevi za izlaznice

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
                            <th>Tip</th>
                            <th>Od</th>
                            <th>Do</th>
                            <th>Napomena</th>
                            <th class="text-end">Akcije</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php if ($requests->num_rows === 0): ?>

                            <tr>

                                <td colspan="6"
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
                                                . ' '
                                                . $row['first_name']
                                        ) ?>

                                    </strong>

                                </td>

                                <td>

                                    <?= htmlspecialchars(
                                        $row['exit_type']
                                    ) ?>

                                </td>

                                <td>

                                    <?= date(
                                        'd.m.Y H:i',
                                        strtotime($row['date_from'])
                                    ) ?>

                                </td>

                                <td>

                                    <?= date(
                                        'd.m.Y H:i',
                                        strtotime($row['date_to'])
                                    ) ?>

                                </td>

                                <td>

                                    <?= htmlspecialchars(
                                        $row['note']
                                    ) ?>

                                </td>

                                <td class="text-end">

                                    <div class="btn-group btn-group-sm">

                                        <a
                                            href="approve_exit_pass.php?id=<?= $row['id'] ?>"
                                            class="btn btn-success">

                                            <i class="bi bi-check-lg"></i>

                                        </a>

                                        <a
                                            href="reject_exit_pass.php?id=<?= $row['id'] ?>"
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