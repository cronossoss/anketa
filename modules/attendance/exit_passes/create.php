<?php

require_once '../../../config/init.php';

require_login();

$pageTitle = 'Nova izlaznica';

include "../../../layouts/layout_start.php";

$employees = $conn->query("

    SELECT

        id,
        first_name,
        last_name

    FROM employees

    ORDER BY
        last_name,
        first_name

");

$types = $conn->query("

    SELECT

        id,
        name

    FROM attendance_exit_types

    WHERE active = 1

    ORDER BY name

");

?>

<div class="container-fluid">

    <?php if (isset($_SESSION['error'])): ?>

        <div class="alert alert-danger alert-dismissible fade show">

            <?= $_SESSION['error'] ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"></button>

        </div>

        <?php unset($_SESSION['error']); ?>

    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>

        <div class="alert alert-success alert-dismissible fade show">

            <?= $_SESSION['success'] ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"></button>

        </div>

        <?php unset($_SESSION['success']); ?>

    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="mb-1">

                Nova izlaznica

            </h3>

            <div class="text-muted">

                Kreiranje izlaznice ili službenog izlaska

            </div>

        </div>

        <a
            href="index.php"
            class="btn btn-outline-secondary">

            Nazad

        </a>

    </div>

    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <form
                method="POST"
                action="store.php">

                <div class="row g-3">

                    <div class="col-md-6">

                        <label class="form-label">

                            Zaposleni

                        </label>

                        <select
                            name="employee_id"
                            class="form-select"
                            required>

                            <option value="">

                                Izaberite zaposlenog

                            </option>

                            <?php while ($employee = $employees->fetch_assoc()): ?>

                                <option
                                    value="<?= $employee['id'] ?>">

                                    <?= htmlspecialchars(

                                        $employee['last_name']
                                            . ' '
                                            . $employee['first_name']

                                    ) ?>

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>

                    <div class="col-md-6">

                        <label class="form-label">

                            Tip izlaska

                        </label>

                        <select
                            name="exit_type_id"
                            class="form-select"
                            required>

                            <option value="">

                                Izaberite tip

                            </option>

                            <?php while ($type = $types->fetch_assoc()): ?>

                                <option
                                    value="<?= $type['id'] ?>">

                                    <?= htmlspecialchars(
                                        $type['name']
                                    ) ?>

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>

                    <div class="col-md-6">

                        <label class="form-label">

                            Od

                        </label>

                        <input
                            type="datetime-local"
                            name="date_from"
                            class="form-control"
                            required>

                    </div>

                    <div class="col-md-6">

                        <label class="form-label">

                            Do

                        </label>

                        <input
                            type="datetime-local"
                            name="date_to"
                            class="form-control"
                            required>

                    </div>

                    <div class="col-md-12">

                        <label class="form-label">

                            Napomena

                        </label>

                        <textarea
                            name="note"
                            rows="4"
                            class="form-control"></textarea>

                    </div>

                </div>

                <hr>

                <div class="d-flex justify-content-end">

                    <button
                        type="submit"
                        class="btn btn-primary">

                        <i class="bi bi-check-lg me-1"></i>

                        Sačuvaj

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<?php include "../../../layouts/admin_layout_end.php"; ?>