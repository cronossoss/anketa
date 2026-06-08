<?php

require_once '../../../config/init.php';

require_login();

$pageTitle = 'Administracija odsustava';

include "../../../layouts/layout_start.php";

$month =
    $_GET['month']
    ?? date('Y-m');

$status =
    $_GET['status']
    ?? '';

$type =
    $_GET['type']
    ?? '';

$where = "1=1";

if ($status) {

    $where .= "
        AND aa.status = '"
        . $conn->real_escape_string($status)
        . "'";
}

if ($type) {

    $where .= "
        AND aa.absence_type_id = "
        . (int)$type;
}

$result = $conn->query("

    SELECT

        aa.id,

        aa.date_from,
        aa.date_to,

        aa.status,

        aa.source,

        aa.note,

        e.first_name,
        e.last_name,

        ou.code AS organizational_unit,

        aat.name AS absence_type

    FROM attendance_absences aa

    JOIN employees e
        ON e.id = aa.employee_id

    LEFT JOIN organizational_units ou
        ON ou.id = e.organizational_unit_id

    JOIN attendance_absence_types aat
        ON aat.id = aa.absence_type_id

    WHERE $where

    ORDER BY aa.date_from DESC

");

$types = $conn->query("

    SELECT

        id,
        name

    FROM attendance_absence_types

    ORDER BY name ASC

");
?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <div class="text-muted">

                Pregled svih odsustava i izlaznica

            </div>

        </div>

        <a
            href="create.php"
            class="btn btn-primary"
        >

            <i class="bi bi-plus-lg me-1"></i>

            Novo odsustvo

        </a>

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
                            class="form-select"
                        >

                            <option value="">

                                Svi

                            </option>

                            <option
                                value="pending"
                                <?= $status == 'pending' ? 'selected' : '' ?>
                            >

                                Na čekanju

                            </option>

                            <option
                                value="approved"
                                <?= $status == 'approved' ? 'selected' : '' ?>
                            >

                                Odobreno

                            </option>

                            <option
                                value="rejected"
                                <?= $status == 'rejected' ? 'selected' : '' ?>
                            >

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
                            class="form-select"
                        >

                            <option value="">

                                Svi tipovi

                            </option>

                            <?php while ($t = $types->fetch_assoc()): ?>

                                <option
                                    value="<?= $t['id'] ?>"
                                    <?= $type == $t['id']
                                        ? 'selected'
                                        : '' ?>
                                >

                                    <?= htmlspecialchars(
                                        $t['name']
                                    ) ?>

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>

                    <div class="col-md-2 d-flex align-items-end">

                        <button
                            class="btn btn-primary w-100"
                        >

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

                <table class="table table-sm table-hover align-middle">

                    <thead>

                        <tr>

                            <th>Zaposleni</th>

                            <th>Tip</th>

                            <th>Period</th>

                            <th>Trajanje</th>

                            <th>Status</th>

                            <th>Kreirao</th>

                            <th>Akcije</th>

                        </tr>

                    </thead>

                    <tbody>

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

                                    <strong>

                                        (<?= htmlspecialchars(
                                            $row['organizational_unit']
                                        ) ?>)

                                        <?= htmlspecialchars(
                                            $row['last_name']
                                            . ' ' .
                                            $row['first_name']
                                        ) ?>

                                    </strong>

                                </td>

                                <td>

                                    <?= htmlspecialchars(
                                        $row['absence_type']
                                    ) ?>

                                </td>

                                <td>

                                    <?= date(
                                        'd.m.Y H:i',
                                        strtotime(
                                            $row['date_from']
                                        )
                                    ) ?>

                                    <br>

                                    <small class="text-muted">

                                        do

                                        <?= date(
                                            'd.m.Y H:i',
                                            strtotime(
                                                $row['date_to']
                                            )
                                        ) ?>

                                    </small>

                                </td>

                                <td>

                                    <?= round(
                                        $durationMinutes / 60,
                                        1
                                    ) ?>h

                                </td>

                                <td>

                                    <?php if ($row['status'] == 'approved'): ?>

                                        <span class="badge bg-success">

                                            Odobreno

                                        </span>

                                    <?php elseif ($row['status'] == 'rejected'): ?>

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
                                            class="btn btn-outline-primary"
                                        >

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

<?php include "../../../layouts/layout_end.php"; ?>