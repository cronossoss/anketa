<?php

$pageTitle = "Pregled importa";

$currentPage = 'asset-import';

include "../../layouts/layout_start.php";

require_login();

require_role(['admin', 'it']);

$importId =
    (int) ($_GET['id'] ?? 0);

$result =
    $conn->query("
        SELECT *
        FROM asset_import_rows
        WHERE import_id = {$importId}
        ORDER BY row_number
    ");
?>

<main class="main-content">

    <div class="page-card">

        <div class="d-flex justify-content-between mb-4">

            <h3>

                Pregled importa

            </h3>

            <form
                method="POST"
                action="process.php">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= csrf_token() ?>">

                <input
                    type="hidden"
                    name="import_id"
                    value="<?= $importId ?>">

                
                <button
                    type="submit"
                    class="btn btn-success">

                    Potvrdi import

                </button>

            </form>

        </div>

        <form
            method="POST"
            action="process.php">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= csrf_token() ?>">

            <input
                type="hidden"
                name="import_id"
                value="<?= $importId ?>">

            <div class="mb-3">

                <label class="form-label">

                    Maksimalan broj redova

                </label>

                <input
                    type="number"
                    name="import_limit"
                    class="form-control"
                    value="100">

            </div>




        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead>

    <tr>

        <th>

            <input
                type="checkbox"
                id="checkAll"
                checked>

                    </th>

                    <th>Red</th>

                    <th>Inventarski broj</th>

                    <th>Kategorija</th>

                    <th>Proizvođač</th>

                    <th>Model</th>

                    <th>Serijski broj</th>

                    <th>Zaposleni</th>

                    <th>Status</th>

                    <th>Poruka</th>

                </tr>

            </thead>
                <tbody>

                    <?php while ($row = $result->fetch_assoc()): ?>

                        <?php

                        $class = match ($row['validation_status']) {

                            'valid' => 'table-success',

                            'warning' => 'table-warning',

                            'error' => 'table-danger',

                            'imported' => 'table-primary',

                            'skipped' => 'table-secondary',

                            default => ''
                        };
                        ?>

                        <tr class="<?= $class ?>">

                            <td>

                                <input
                                    type="checkbox"
                                    name="selected_rows[]"
                                    value="<?= $row['id'] ?>"
                                    checked>

                            </td>

                            <td>

                                <?= $row['row_number'] ?>

                            </td>

                            <td>

                                <input
                                    type="text"
                                    name="inventory_number[<?= $row['id'] ?>]"
                                    value="<?= e($row['inventory_number']) ?>"
                                    class="form-control form-control-sm">

                            </td>

                            <td>

                                <input
                                    type="text"
                                    name="category_name[<?= $row['id'] ?>]"
                                    value="<?= e($row['category_name']) ?>"
                                    class="form-control form-control-sm">

                            </td>

                            <td>

                                <input
                                    type="text"
                                    name="manufacturer[<?= $row['id'] ?>]"
                                    value="<?= e($row['manufacturer']) ?>"
                                    class="form-control form-control-sm">

                            </td>

                            <td>

                                <input
                                    type="text"
                                    name="model[<?= $row['id'] ?>]"
                                    value="<?= e($row['model']) ?>"
                                    class="form-control form-control-sm">

                            </td>

                            <td>

                                <input
                                    type="text"
                                    name="serial_number[<?= $row['id'] ?>]"
                                    value="<?= e($row['serial_number']) ?>"
                                    class="form-control form-control-sm">

                            </td>

                            <td>

                                <div class="fw-semibold">

                                    <?= e($row['employee_name']) ?>

                                </div>

                                <div class="small text-muted">

                                    <?= e($row['employee_personal_number']) ?>

                                </div>

                            </td>

                            <td>

                                <span class="badge bg-secondary">

                                    <?= e($row['validation_status']) ?>

                                </span>

                            </td>

                            <td>

                                <small>

                                    <?= e($row['validation_message']) ?>

                                </small>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                    </tbody>

            </table>

        </div>

    <div class="mt-3">

    <button
        type="submit"
        class="btn btn-success">

        Potvrdi import

    </button>

</div>

</form>

    </div>

</main>

<script>

document
    .getElementById('checkAll')
    .addEventListener(
        'change',
        function () {

            document
                .querySelectorAll(
                    'input[name=\"selected_rows[]\"]'
                )
                .forEach(cb => {

                    cb.checked =
                        this.checked;
                });
        }
    );

</script>

<?php include "../../layouts/footer.php"; ?>