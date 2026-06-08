<?php

require_once '../../../config/init.php';

$currentPage = 'attendance-assignments';

$pageTitle = "Dodela rasporeda";

include "../../../layouts/layout_start.php";

$assignments = $conn->query("

    SELECT

        asa.*,

        aws.name AS schedule_name,

        CONCAT(
            e.first_name,
            ' ',
            e.last_name
        ) AS employee_name,

        ou.name AS organizational_unit_name

    FROM attendance_schedule_assignments asa

    JOIN attendance_work_schedules aws
        ON aws.id = asa.schedule_id

    LEFT JOIN employees e
        ON e.id = asa.employee_id

    LEFT JOIN organizational_units ou
        ON ou.id = asa.organizational_unit_id

    ORDER BY asa.id DESC

");

$schedules = $conn->query("
    SELECT *
    FROM attendance_work_schedules
    WHERE active = 1
    ORDER BY name ASC
");

$employees = $conn->query("
    SELECT
        id,
        personal_id,
        first_name,
        last_name
    FROM employees
    ORDER BY last_name ASC
");

$units = $conn->query("
    SELECT
        id,
        code,
        name
    FROM organizational_units
    WHERE type = 'OJ'
    ORDER BY name ASC
");

?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="mb-1">
                Dodela rasporeda
            </h3>

            <div class="text-muted">

                Povezivanje zaposlenih i OJ sa rasporedima rada

            </div>

        </div>

        <button
            class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#assignmentModal"
        >

            Nova dodela

        </button>

    </div>

    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <table class="table table-hover align-middle">

                <thead>

                    <tr>

                        <th>Tip</th>

                        <th>Zaposleni / OJ</th>

                        <th>Raspored</th>

                        <th>Važi od</th>

                    </tr>

                </thead>

                <tbody>

                    <?php while ($row = $assignments->fetch_assoc()): ?>

                        <?php

                        $isEmployee =
                            !empty($row['employee_id']);

                        ?>

                        <tr>

                            <td>

                                <?php if ($isEmployee): ?>

                                    <span class="badge bg-primary">

                                        Zaposleni

                                    </span>

                                <?php else: ?>

                                    <span class="badge bg-info text-dark">

                                        Organizaciona jedinica

                                    </span>

                                <?php endif; ?>

                            </td>

                            <td>

                                <?= htmlspecialchars(

                                    $isEmployee
                                        ? $row['employee_name']
                                        : $row['organizational_unit_name']

                                ) ?>

                            </td>

                            <td>

                                <?= htmlspecialchars(
                                    $row['schedule_name']
                                ) ?>

                            </td>

                            <td>

                                <?= $row['valid_from'] ?>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<!-- MODAL -->

<div
    class="modal fade"
    id="assignmentModal"
    tabindex="-1"
>

    <div class="modal-dialog">

        <div class="modal-content">

            <form
                method="POST"
                action="../actions/create_assignment.php"
            >

                <div class="modal-header">

                    <h5 class="modal-title">

                        Nova dodela rasporeda

                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                    ></button>

                </div>

                <div class="modal-body">

                    <div class="mb-3">

                        <label class="form-label">

                            Raspored rada

                        </label>

                        <select
                            name="schedule_id"
                            class="form-select"
                            required
                        >

                            <option value="">
                                Izaberi
                            </option>

                            <?php while ($schedule = $schedules->fetch_assoc()): ?>

                                <option value="<?= $schedule['id'] ?>">

                                    <?= htmlspecialchars(
                                        $schedule['name']
                                    ) ?>

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">

                            Zaposleni

                        </label>

                        <select
                            name="employee_id"
                            class="form-select"
                        >

                            <option value="">
                                Nije izabrano
                            </option>

                            <?php while ($employee = $employees->fetch_assoc()): ?>

                                <option value="<?= $employee['id'] ?>">

                                    <?= htmlspecialchars(

                                        $employee['personal_id']
                                        . ' - '
                                        . $employee['last_name']
                                        . ' '
                                        . $employee['first_name']

                                    ) ?>

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">

                            Organizaciona jedinica

                        </label>

                        <select
                            name="organizational_unit_id"
                            class="form-select"
                        >

                            <option value="">
                                Nije izabrano
                            </option>

                            <?php while ($unit = $units->fetch_assoc()): ?>

                                <option value="<?= $unit['id'] ?>">

                                    <?= htmlspecialchars(

                                        $unit['code']
                                        . ' - '
                                        . $unit['name']

                                    ) ?>

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">

                            Važi od

                        </label>

                        <input
                            type="date"
                            name="valid_from"
                            class="form-control"
                            value="<?= date('Y-m-d') ?>"
                            required
                        >

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-light"
                        data-bs-dismiss="modal"
                    >

                        Otkaži

                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >

                        Sačuvaj

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<?php include "../../../layouts/admin_layout_end.php"; ?>