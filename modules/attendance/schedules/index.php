<?php

require_once '../../../config/init.php';

$currentPage = 'attendance-schedules';

$pageTitle = "Rasporedi rada";

include "../../../layouts/admin_layout_start.php";

$schedules = $conn->query("
    SELECT *
    FROM attendance_work_schedules
    ORDER BY name ASC
");

?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="mb-1">
                Rasporedi rada
            </h3>

            <div class="text-muted">

                Definisanje tipova rada i smena

            </div>

        </div>

        <button
            class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#scheduleModal">

            Novi raspored

        </button>

    </div>

    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <table class="table table-hover align-middle">

                <thead>

                    <tr>

                        <th>Naziv</th>

                        <th>Početak</th>

                        <th>Kraj</th>

                        <th>Tolerancija</th>

                        <th>Radni dani</th>

                        <th>Status</th>

                    </tr>

                </thead>

                <tbody>

                    <?php while ($row = $schedules->fetch_assoc()): ?>

                        <tr>

                            <td>

                                <strong>

                                    <?= htmlspecialchars(
                                        $row['name']
                                    ) ?>

                                </strong>

                            </td>

                            <td>

                                <?= substr(
                                    $row['expected_start'],
                                    0,
                                    5
                                ) ?>

                            </td>

                            <td>

                                <?= substr(
                                    $row['expected_end'],
                                    0,
                                    5
                                ) ?>

                            </td>

                            <td>

                                <?= $row['late_tolerance_minutes'] ?>
                                min

                            </td>

                            <td>

                                <?= htmlspecialchars(
                                    $row['work_days']
                                ) ?>

                            </td>

                            <td>

                                <?php if ($row['active']): ?>

                                    <span class="badge bg-success">
                                        Aktivan
                                    </span>

                                <?php else: ?>

                                    <span class="badge bg-secondary">
                                        Neaktivan
                                    </span>

                                <?php endif; ?>

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
    id="scheduleModal"
    tabindex="-1">

    <div class="modal-dialog">

        <div class="modal-content">

            <form
                method="POST"
                action="../actions/create_schedule.php">

                <div class="modal-header">

                    <h5 class="modal-title">

                        Novi raspored rada

                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>

                </div>

                <div class="modal-body">

                    <div class="mb-3">

                        <label class="form-label">
                            Naziv
                        </label>

                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            required>

                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Početak rada
                            </label>

                            <input
                                type="time"
                                name="expected_start"
                                class="form-control"
                                required>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Kraj rada
                            </label>

                            <input
                                type="time"
                                name="expected_end"
                                class="form-control"
                                required>

                        </div>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Tolerancija kašnjenja (min)
                        </label>

                        <input
                            type="number"
                            name="late_tolerance_minutes"
                            class="form-control"
                            value="5">

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Radni dani
                        </label>

                        <input
                            type="text"
                            name="work_days"
                            class="form-control"
                            value="1,2,3,4,5">

                        <small class="text-muted">

                            1=ponedeljak ... 7=nedelja

                        </small>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-light"
                        data-bs-dismiss="modal">

                        Otkaži

                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary">

                        Sačuvaj

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<?php include "../../../layouts/admin_layout_end.php"; ?>