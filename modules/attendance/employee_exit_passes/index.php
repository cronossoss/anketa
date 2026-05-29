<?php

require_once '../../../config/init.php';

require_login();

$pageTitle =
    'Moje izlaznice';

include "../../../layouts/admin_layout_start.php";

$userId =
    $_SESSION['user_id'];

$employeeId = $conn->query("

    SELECT employee_id

    FROM users

    WHERE id = {$userId}

")->fetch_assoc()['employee_id'];

$stmt = $conn->prepare("

    SELECT

        ep.*,

        et.name AS exit_type

    FROM attendance_exit_passes ep

    JOIN attendance_exit_types et
        ON et.id = ep.exit_type_id

    WHERE ep.employee_id = ?

    ORDER BY ep.created_at DESC

");

$stmt->bind_param(
    'i',
    $employeeId
);

$stmt->execute();

$result =
    $stmt->get_result();

?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="mb-1">

                Moje izlaznice

            </h3>

            <div class="text-muted">

                Pregled svih zahteva za izlazak

            </div>

        </div>

        <a
            href="create.php"
            class="btn btn-primary"
        >

            <i class="bi bi-plus-lg me-1"></i>

            Nova izlaznica

        </a>

    </div>

    <div class="card border-0 shadow-sm">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead>

                        <tr>

                            <th>Tip</th>

                            <th>Period</th>

                            <th>Trajanje</th>

                            <th>Status</th>

                            <th>Napomena</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php if ($result->num_rows == 0): ?>

                            <tr>

                                <td
                                    colspan="5"
                                    class="text-center text-muted py-4"
                                >

                                    Nemate evidentiranih izlaznica.

                                </td>

                            </tr>

                        <?php endif; ?>

                        <?php while ($row = $result->fetch_assoc()): ?>

                            <?php

                            $durationMinutes = round(

                                (
                                    strtotime($row['date_to'])
                                    -
                                    strtotime($row['date_from'])
                                ) / 60

                            );

                            ?>

                            <tr>

                                <td>

                                    <?= htmlspecialchars(
                                        $row['exit_type']
                                    ) ?>

                                </td>

                                <td>

                                    <div>

                                        <?= date(
                                            'd.m.Y H:i',
                                            strtotime(
                                                $row['date_from']
                                            )
                                        ) ?>

                                    </div>

                                    <div class="text-muted small">

                                        do

                                        <?= date(
                                            'd.m.Y H:i',
                                            strtotime(
                                                $row['date_to']
                                            )
                                        ) ?>

                                    </div>

                                </td>

                                <td>

                                    <?= round(
                                        $durationMinutes / 60,
                                        1
                                    ) ?>h

                                </td>

                                <td>

                                    <?php if ($row['status'] === 'approved'): ?>

                                        <span class="badge bg-success">

                                            Odobreno

                                        </span>

                                    <?php elseif ($row['status'] === 'rejected'): ?>

                                        <span class="badge bg-danger">

                                            Odbijeno

                                        </span>

                                    <?php else: ?>

                                        <span class="badge bg-warning text-dark">

                                            Na čekanju

                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td>

                                    <?= nl2br(
                                        htmlspecialchars(
                                            $row['note'] ?? ''
                                        )
                                    ) ?>

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