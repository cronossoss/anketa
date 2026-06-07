<?php

require_once '../../../config/init.php';

require_login();
require_role(['admin', 'manager']);

require_once '../helpers/organization.php';

$pageTitle = 'Novo odsustvo';

include "../../../layouts/admin_layout_start.php";

$employees = [];

if (has_role('admin')) {

    $employeesResult = $conn->query("

        SELECT
            id,
            first_name,
            last_name
        FROM employees
        ORDER BY last_name, first_name

    ");
} else {

    $managerEmployeeId =
        getManagerEmployeeId(
            $conn,
            $_SESSION['user_id']
        );

    $unitIds =
        getManagedOrganizationUnits(
            $conn,
            $managerEmployeeId
        );

    if (!empty($unitIds)) {

        $placeholders =
            implode(
                ',',
                array_fill(
                    0,
                    count($unitIds),
                    '?'
                )
            );

        $sql = "

            SELECT
                id,
                first_name,
                last_name
            FROM employees
            WHERE organizational_unit_id
                IN ($placeholders)
            ORDER BY last_name, first_name

        ";

        $stmt = $conn->prepare($sql);

        $types =
            str_repeat(
                'i',
                count($unitIds)
            );

        $stmt->bind_param(
            $types,
            ...$unitIds
        );

        $stmt->execute();

        $employeesResult =
            $stmt->get_result();
    }
}

$absenceTypes = $conn->query("

    SELECT
        id,
        name
    FROM attendance_absence_types
    WHERE active = 1
    ORDER BY name

");

?>

<div class="container-fluid">

    <div class="card border-0 shadow-sm">

        <div class="card-header">

            <h4 class="mb-0">

                Novo odsustvo

            </h4>

        </div>

        <div class="card-body">

            <form
                method="POST"
                action="store.php"
                enctype="multipart/form-data">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= csrf_token() ?>">

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

                            <?php while ($employee = $employeesResult->fetch_assoc()): ?>

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

                            Tip odsustva

                        </label>

                        <select
                            name="absence_type_id"
                            class="form-select"
                            required>

                            <option value="">

                                Izaberite tip

                            </option>

                            <?php while ($type = $absenceTypes->fetch_assoc()): ?>

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
                            type="date"
                            name="date_from"
                            class="form-control"
                            required>

                    </div>

                    <div class="col-md-6">

                        <label class="form-label">

                            Do

                        </label>

                        <input
                            type="date"
                            name="date_to"
                            class="form-control"
                            required>

                    </div>

                    <div class="col-12">

                        <label class="form-label">

                            Napomena

                        </label>

                        <textarea
                            name="note"
                            rows="4"
                            class="form-control"></textarea>

                    </div>

                    <div class="col-12">

                        <label class="form-label">

                            Dokument

                        </label>

                        <input
                            type="file"
                            name="document"
                            class="form-control">

                    </div>

                </div>

                <hr>

                <div class="d-flex justify-content-end gap-2">

                    <a
                        href="index.php"
                        class="btn btn-secondary">

                        Otkaži

                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary">

                        Sačuvaj odsustvo

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<?php include "../../../layouts/admin_layout_end.php"; ?>