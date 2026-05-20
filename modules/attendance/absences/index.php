<?php

require_once '../../../config/init.php';

$currentPage = 'attendance-absences';

$pageTitle = "Odsustva";

include "../../../layouts/admin_layout_start.php";

$absences = $conn->query("

    SELECT

        aa.*,

        CONCAT(
            e.personal_id,
            ' - ',
            e.last_name,
            ' ',
            e.first_name
        ) AS employee_name,

        aat.name AS absence_type_name

    FROM attendance_absences aa

    JOIN employees e
        ON e.id = aa.employee_id

    JOIN attendance_absence_types aat
        ON aat.id = aa.absence_type_id

    ORDER BY aa.date_from DESC

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

$absenceTypes = $conn->query("
    SELECT *
    FROM attendance_absence_types
    WHERE active = 1
    ORDER BY name ASC
");

?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="mb-1">
                Odsustva
            </h3>

            <div class="text-muted">

                Evidencija odobrenih odsustava i izlaza

            </div>

        </div>

        <button
            class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#absenceModal"
        >

            Novo odsustvo

        </button>

    </div>

    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <table class="table table-hover align-middle">

                <thead>

                    <tr>

                        <th>Radnik</th>

                        <th>Vrsta</th>

                        <th>Od</th>

                        <th>Do</th>

                        <th>Napomena</th>

                    </tr>

                </thead>

                <tbody>

                    <?php while ($row = $absences->fetch_assoc()): ?>

                        <tr>

                            <td>

                                <?= htmlspecialchars(
                                    $row['employee_name']
                                ) ?>

                            </td>

                            <td>

                                <span class="badge bg-info text-dark">

                                    <?= htmlspecialchars(
                                        $row['absence_type_name']
                                    ) ?>

                                </span>

                            </td>

                            <td>

                                <?= $row['date_from'] ?>

                            </td>

                            <td>

                                <?= $row['date_to'] ?>

                            </td>

                            <td>

                                <?= htmlspecialchars(
                                    $row['note'] ?? ''
                                ) ?>

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
    id="absenceModal"
    tabindex="-1"
>

    <div class="modal-dialog">

        <div class="modal-content">

            <form
                method="POST"
                action="../actions/create_absence.php"
            >

                <div class="modal-header">

                    <h5 class="modal-title">

                        Novo odsustvo

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

                            Radnik

                        </label>

                        <select
                            name="employee_id"
                            class="form-select"
                            required
                        >

                            <option value="">
                                Izaberi
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

                            Vrsta odsustva

                        </label>

                        <select
                            name="absence_type_id"
                            class="form-select"
                            required
                        >

                            <option value="">
                                Izaberi
                            </option>

                            <?php while ($type = $absenceTypes->fetch_assoc()): ?>

                                <option value="<?= $type['id'] ?>">

                                    <?= htmlspecialchars(
                                        $type['name']
                                    ) ?>

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">

                            Od

                        </label>

                        <input
                            type="datetime-local"
                            name="date_from"
                            class="form-control"
                            required
                        >

                    </div>

                    <div class="mb-3">

                        <label class="form-label">

                            Do

                        </label>

                        <input
                            type="datetime-local"
                            name="date_to"
                            class="form-control"
                            required
                        >

                    </div>

                    <div class="mb-3">

                        <label class="form-label">

                            Napomena

                        </label>

                        <textarea
                            name="note"
                            class="form-control"
                            rows="3"
                        ></textarea>

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