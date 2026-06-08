<?php

require_once '../../../config/init.php';

$currentPage = 'attendance-absence-types';

$pageTitle = "Vrste odsustva";

include "../../../layouts/layout_start.php";

$types = $conn->query("
    SELECT *
    FROM attendance_absence_types
    ORDER BY name ASC
");

?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <div class="text-muted">

                Definisanje tipova odsustva i opravdanja

            </div>

        </div>

        <button
            class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#absenceTypeModal"
        >

            Nova vrsta odsustva

        </button>

    </div>

    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <table class="table table-hover align-middle">

                <thead>

                    <tr>

                        <th>Naziv</th>

                        <th>Šifra</th>

                        <th>Plaćeno</th>

                        <th>Aktivno</th>

                    </tr>

                </thead>

                <tbody>

                    <?php while ($row = $types->fetch_assoc()): ?>

                        <tr>

                            <td>

                                <strong>

                                    <?= htmlspecialchars(
                                        $row['name']
                                    ) ?>

                                </strong>

                            </td>

                            <td>

                                <?= htmlspecialchars(
                                    $row['code']
                                ) ?>

                            </td>

                            <td>

                                <?php if ($row['paid']): ?>

                                    <span class="badge bg-success">

                                        Da

                                    </span>

                                <?php else: ?>

                                    <span class="badge bg-danger">

                                        Ne

                                    </span>

                                <?php endif; ?>

                            </td>

                            <td>

                                <?php if ($row['active']): ?>

                                    <span class="badge bg-success">

                                        Aktivno

                                    </span>

                                <?php else: ?>

                                    <span class="badge bg-secondary">

                                        Neaktivno

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
    id="absenceTypeModal"
    tabindex="-1"
>

    <div class="modal-dialog">

        <div class="modal-content">

            <form
                method="POST"
                action="../actions/create_absence_type.php"
            >

                <div class="modal-header">

                    <h5 class="modal-title">

                        Nova vrsta odsustva

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

                            Naziv

                        </label>

                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            required
                        >

                    </div>

                    <div class="mb-3">

                        <label class="form-label">

                            Šifra

                        </label>

                        <input
                            type="text"
                            name="code"
                            class="form-control"
                        >

                    </div>

                    <div class="form-check mb-3">

                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="paid"
                            value="1"
                            checked
                        >

                        <label class="form-check-label">

                            Plaćeno odsustvo

                        </label>

                    </div>

                    <div class="form-check">

                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="active"
                            value="1"
                            checked
                        >

                        <label class="form-check-label">

                            Aktivno

                        </label>

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

<?php include "../../../layouts/layout_end.php"; ?>