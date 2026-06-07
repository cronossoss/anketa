<?php

require_once '../../../config/init.php';

require_login();
require_role(['admin', 'hr', 'manager', 'user']);

require_once '../helpers/organization.php';

$pageTitle = 'Evidencija odsustava';

include "../../../layouts/admin_layout_start.php";

$status = $_GET['status'] ?? '';
$type   = $_GET['type'] ?? '';

$where = [];
$params = [];

if (has_role('manager') && !has_role('admin') && !has_role('hr')) {

    $managerEmployeeId = getManagerEmployeeId(
        $conn,
        $_SESSION['user_id']
    );

    $unitIds = getManagedOrganizationUnits(
        $conn,
        $managerEmployeeId
    );

    if (!empty($unitIds)) {

        $placeholders = implode(
            ',',
            array_fill(0, count($unitIds), '?')
        );

        $where[] =
            "e.organizational_unit_id IN ($placeholders)";

        $params = array_merge(
            $params,
            $unitIds
        );
    } else {

        $where[] = "1=0";
    }
}

if (!empty($status)) {

    $where[] = "aa.status = ?";
    $params[] = $status;
}

if (!empty($type)) {

    $where[] = "aa.absence_type_id = ?";
    $params[] = (int)$type;
}

$whereSql =
    empty($where)
    ? '1=1'
    : implode(' AND ', $where);

$sql = "

    SELECT

        aa.id,
        aa.date_from,
        aa.date_to,
        aa.status,
        aa.source,
        aa.note,

        e.first_name,
        e.last_name,

        ou.name AS organizational_unit_name,
        ou.code AS organizational_unit_code,

        aat.name AS absence_type

    FROM attendance_absences aa

    JOIN employees e
        ON e.id = aa.employee_id

    LEFT JOIN organizational_units ou
        ON ou.id = e.organizational_unit_id

    JOIN attendance_absence_types aat
        ON aat.id = aa.absence_type_id

    WHERE $whereSql

    ORDER BY aa.date_from DESC

";

$stmt = $conn->prepare($sql);

if (!empty($params)) {

    $bindTypes = '';

    if (
        has_role('manager')
        && !has_role('admin')
        && !has_role('hr')
    ) {

        $bindTypes .= str_repeat(
            'i',
            count($unitIds)
        );
    }

    if (!empty($status)) {
        $bindTypes .= 's';
    }

    if (!empty($type)) {
        $bindTypes .= 'i';
    }

    $stmt->bind_param(
        $bindTypes,
        ...$params
    );
}

$stmt->execute();

$result = $stmt->get_result();

$typesStmt = $conn->query("

    SELECT
        id,
        name
    FROM attendance_absence_types
    WHERE active = 1
    ORDER BY name ASC

");

$types = [];
while ($row = $typesStmt->fetch_assoc()) {
    $types[] = $row;
}

?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="mb-1">
                Evidencija odsustava
            </h3>

            <div class="text-muted">
                Pregled evidentiranih odsustava zaposlenih
            </div>

        </div>

        <?php if (
            has_role('manager')
            || has_role('admin')
        ): ?>

            <a
                href="create.php"
                class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>
                Novo odsustvo
            </a>

        <?php endif; ?>

    </div>

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <form method="GET">

                <div class="row g-3">

                    <div class="col-md-3">

                        <label class="form-label">
                            Status
                        </label>

                        <select
                            name="status"
                            class="form-select">

                            <option value="">
                                Svi
                            </option>

                            <option
                                value="pending"
                                <?= $status == 'pending' ? 'selected' : '' ?>>
                                Na čekanju
                            </option>

                            <option
                                value="approved"
                                <?= $status == 'approved' ? 'selected' : '' ?>>
                                Odobreno
                            </option>

                            <option
                                value="rejected"
                                <?= $status == 'rejected' ? 'selected' : '' ?>>
                                Odbijeno
                            </option>

                        </select>

                    </div>

                    <div class="col-md-4">

                        <label class="form-label">
                            Tip odsustva
                        </label>

                        <select
                            name="type"
                            class="form-select">

                            <option value="">
                                Svi tipovi
                            </option>

                            <?php foreach ($types as $t): ?>

                                <option
                                    value="<?= $t['id'] ?>"
                                    <?= $type == $t['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($t['name']) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="col-md-2 d-flex align-items-end">

                        <button
                            type="submit"
                            class="btn btn-primary w-100">
                            Prikaži
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead>

                        <tr>

                            <th>Zaposleni</th>
                            <th>Tip</th>
                            <th>Period</th>
                            <th>Status</th>
                            <th>Izvor</th>
                            <th>Akcije</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php while ($row = $result->fetch_assoc()): ?>

                            <tr>

                                <td>

                                    <strong>

                                        (<?= htmlspecialchars($row['organizational_unit_code']) ?>)

                                        <?= htmlspecialchars(
                                            $row['last_name']
                                                . ' ' .
                                                $row['first_name']
                                        ) ?>

                                    </strong>

                                    <br>

                                    <small class="text-muted">

                                        <?= htmlspecialchars(
                                            $row['organizational_unit_name']
                                        ) ?>

                                    </small>

                                </td>

                                <td>

                                    <?= htmlspecialchars(
                                        $row['absence_type']
                                    ) ?>

                                </td>

                                <td>

                                    <?= date(
                                        'd.m.Y',
                                        strtotime(
                                            $row['date_from']
                                        )
                                    ) ?>

                                    -

                                    <?= date(
                                        'd.m.Y',
                                        strtotime(
                                            $row['date_to']
                                        )
                                    ) ?>

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

                                    <?= htmlspecialchars(
                                        $row['source']
                                    ) ?>

                                </td>

                                <td>

                                    <div class="btn-group btn-group-sm">

                                        <a
                                            href="edit.php?id=<?= $row['id'] ?>"
                                            class="btn btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
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