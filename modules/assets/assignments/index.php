<?php

$pageTitle = "Zaduženja";

$currentPage = 'asset-assignments';

include "../../../layouts/admin_layout_start.php";

require_login();

require_role(['admin', 'it']);

$result = $conn->query("
    SELECT
        aa.id,

        aa.asset_id,

        aa.employee_id,

        aa.assigned_at,

        e.personal_id,

        e.first_name,
        e.last_name,

        ou.name AS org_unit_name,

        a.inventory_number,

        a.manufacturer,
        a.model,

        c.name AS category_name

    FROM asset_assignments aa

    LEFT JOIN employees e
        ON e.id = aa.employee_id

    LEFT JOIN organizational_units ou
        ON ou.id = e.organizational_unit_id

    LEFT JOIN assets a
        ON a.id = aa.asset_id

    LEFT JOIN asset_categories c
        ON c.id = a.category_id

    WHERE aa.returned_at IS NULL

    ORDER BY
        e.first_name,
        e.last_name
");
?>

<main class="main-content">

    <div class="page-card">

        <h4 class="mb-4">

            Aktivna zaduženja

        </h4>

        <div class="table-wrapper">

            <?php if (!empty($_SESSION['success'])): ?>

                <div class="alert alert-success">

                    <?= $_SESSION['success'] ?>

                </div>

                <?php unset($_SESSION['success']); ?>

            <?php endif; ?>

            <table class="table table-hover align-middle">

                <thead>

                    <tr>

                        <th>Matični broj</th>

                        <th>Zaposleni</th>

                        <th>OJ</th>

                        <th>Inventar</th>

                        <th>Inventarski broj</th>

                        <th width="180">

                            Akcije

                        </th>

                    </tr>

                </thead>

                <tbody>

                    <?php while ($row = $result->fetch_assoc()): ?>

                        <tr
                            data-bs-toggle="collapse"
                            data-bs-target="#details-<?= $row['id'] ?>"
                            style="cursor:pointer;">

                            <td>

                                <?= e($row['personal_id']) ?>

                            </td>

                            <td>

                                <?= e(
                                    $row['first_name']
                                        . ' '
                                        . $row['last_name']
                                ) ?>

                            </td>

                            <td>

                                <?= e($row['org_unit_name']) ?>

                            </td>

                            <td>

                                <?= e(
                                    $row['manufacturer']
                                        . ' '
                                        . $row['model']
                                ) ?>

                            </td>

                            <td>

                                <?= e($row['inventory_number']) ?>

                            </td>

                            <td>

                                <button
                                    type="button"
                                    class="btn btn-sm btn-warning change-assignment-btn"

                                    data-id="<?= $row['id'] ?>"
                                    data-employee-id="<?= $row['employee_id'] ?>"
                                    data-asset-id="<?= $row['asset_id'] ?>"

                                    data-bs-toggle="modal"
                                    data-bs-target="#changeAssignmentModal">

                                    Promena

                                </button>

                                <form
                                    method="POST"
                                    action="../actions/assignment_return.php"
                                    class="d-inline">

                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?= csrf_token() ?>">

                                    <input
                                        type="hidden"
                                        name="assignment_id"
                                        value="<?= $row['id'] ?>">

                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('Razdužiti inventar?')">

                                        Razduži

                                    </button>

                                </form>

                            </td>

                        </tr>

                        <tr>

                            <td colspan="6" class="p-0 border-0">

                                <div
                                    class="collapse"
                                    id="details-<?= $row['id'] ?>">

                                    <div class="p-3 bg-light">

                                        <h6>

                                            Istorija inventara

                                        </h6>

                                        <?php

                                        $historyStmt =
                                            $conn->prepare("
                                                SELECT
                                                    aa.assigned_at,
                                                    aa.returned_at,

                                                    e.first_name,
                                                    e.last_name

                                                FROM asset_assignments aa

                                                LEFT JOIN employees e
                                                    ON e.id = aa.employee_id

                                                WHERE aa.asset_id = ?

                                                ORDER BY aa.assigned_at DESC
                                            ");

                                        $historyStmt->bind_param(
                                            "i",
                                            $row['asset_id']
                                        );

                                        $historyStmt->execute();

                                        $history =
                                            $historyStmt
                                            ->get_result();
                                        ?>

                                        <ul class="list-group">

                                            <?php while ($h = $history->fetch_assoc()): ?>

                                                <li class="list-group-item">

                                                    <strong>

                                                        <?= e(
                                                            $h['first_name']
                                                                . ' '
                                                                . $h['last_name']
                                                        ) ?>

                                                    </strong>

                                                    |

                                                    <?= e(
                                                        $h['assigned_at']
                                                    ) ?>

                                                    →

                                                    <?= e(
                                                        $h['returned_at']
                                                            ?? 'Aktivno'
                                                    ) ?>

                                                </li>

                                            <?php endwhile; ?>

                                        </ul>

                                    </div>

                                </div>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    </div>

</main>

<div
    class="modal fade"
    id="changeAssignmentModal"
    tabindex="-1">

    <div class="modal-dialog">

        <div class="modal-content">

            <form
                method="POST"
                action="../actions/assignment_change.php">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= csrf_token() ?>">

                <input
                    type="hidden"
                    name="assignment_id"
                    id="change_assignment_id">

                <input
                    type="hidden"
                    name="asset_id"
                    id="change_asset_id">

                <div class="modal-header">

                    <h5 class="modal-title">

                        Promena zaduženja

                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>

                </div>

                <div class="modal-body">

                    <div class="mb-3">

                        <label class="form-label">

                            Novi zaposleni

                        </label>

                        <select
                            name="employee_id"
                            class="form-select"
                            required>

                            <option value="">
                                Izaberi zaposlenog
                            </option>

                            <?php

                            $employees = $conn->query("
                                SELECT
                                    id,
                                    first_name,
                                    last_name
                                FROM employees
                                ORDER BY
                                    first_name,
                                    last_name
                            ");

                            while ($emp = $employees->fetch_assoc()):
                            ?>

                                <option value="<?= $emp['id'] ?>">

                                    <?= e(
                                        $emp['first_name']
                                            . ' '
                                            . $emp['last_name']
                                    ) ?>

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">

                            Napomena

                        </label>

                        <textarea
                            name="note"
                            class="form-control"
                            rows="3"></textarea>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="submit"
                        class="btn btn-warning">

                        Promeni zaduženje

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<script>
    document
        .querySelectorAll('.change-assignment-btn')
        .forEach(button => {

            button.addEventListener('click', () => {

                document.getElementById(
                        'change_assignment_id'
                    ).value =
                    button.dataset.id;

                document.getElementById(
                        'change_asset_id'
                    ).value =
                    button.dataset.assetId;
            });
        });
</script>

<?php include "../../../layouts/footer.php"; ?>