<?php

require_once '../../../config/init.php';

$currentPage = 'attendance-daily-report';

$pageTitle = 'Dnevni izveštaj prisustva';

include "../../../layouts/admin_layout_start.php";

//
// FILTERI
//

$date = $_GET['date'] ?? date('Y-m-d');

// Validacija datuma
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    $date = date('Y-m-d');
}

$organizationalUnit = isset($_GET['organizational_unit']) ? (int)$_GET['organizational_unit'] : 0;

//
// OJ LISTA
//

$ouResult = $conn->query("
    SELECT id, code, name
    FROM organizational_units
    ORDER BY code ASC
");

//
// QUERY - SADA SIGURAN (prepare + bind_param)
//

$sql = "
    SELECT
        e.id,
        e.first_name,
        e.last_name,
        ou.code AS organizational_unit,
        ou.name AS organizational_unit_name,
        ads.first_in,
        ads.last_out,
        ads.late_minutes,
        ads.early_leave_minutes,
        ads.presence_status,
        ads.reason_label,
        ads.is_justified,
        (
            SELECT al.direction
            FROM attendance_logs al
            WHERE al.employee_id = e.id
            ORDER BY al.access_datetime DESC
            LIMIT 1
        ) AS last_direction
    FROM attendance_daily_summary ads
    JOIN employees e ON e.id = ads.employee_id
    LEFT JOIN organizational_units ou ON ou.id = e.organizational_unit_id
    WHERE ads.work_date = ?
";

$params = [$date];
$types = "s";

if ($organizationalUnit > 0) {
    $sql .= " AND ou.id = ?";
    $params[] = $organizationalUnit;
    $types .= "i";
}

$sql .= " ORDER BY ou.code ASC, e.last_name ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

//
// KPI
//

$present = 0;
$late = 0;
$absent = 0;
$justified = 0;
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;

    if ($row['presence_status'] === 'present') {
        $present++;
    } elseif ($row['presence_status'] === 'late') {
        $late++;
    } elseif ($row['is_justified']) {
        $justified++;
    } else {
        $absent++;
    }
}

?>

<!-- HTML ostaje ISTI kao u originalu -->
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Dnevni izveštaj prisustva</h3>
            <div class="text-muted">Pregled attendance podataka po danu</div>
        </div>
    </div>

    <!-- FILTERI -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Datum</label>
                        <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($date) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Organizaciona jedinica</label>
                        <select name="organizational_unit" class="form-select">
                            <option value="">Sve organizacione jedinice</option>
                            <?php while ($ou = $ouResult->fetch_assoc()): ?>
                                <option value="<?= $ou['id'] ?>" <?= $organizationalUnit == $ou['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($ou['code'] . ' - ' . $ou['name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-1"></i> Prikaži
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- KPI -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted">Prisutni</div>
                    <div class="fs-2 fw-bold text-success"><?= $present ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted">Kašnjenja</div>
                    <div class="fs-2 fw-bold text-warning"><?= $late ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted">Opravdani izlazi</div>
                    <div class="fs-2 fw-bold text-info"><?= $justified ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted">Odsutni</div>
                    <div class="fs-2 fw-bold text-danger"><?= $absent ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- TABELA -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Zaposleni</th>
                            <th>OJ</th>
                            <th>Ulaz</th>
                            <th>Izlaz</th>
                            <th>Kašnjenje</th>
                            <th>Raniji izlaz</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $row): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($row['last_name'] . ' ' . $row['first_name']) ?></strong></td>
                                <td><?= htmlspecialchars($row['organizational_unit'] ?? '-') ?></td>
                                <td><?= $row['first_in'] ? date('H:i', strtotime($row['first_in'])) : '-' ?></td>
                                <td><?= $row['last_out'] ? date('H:i', strtotime($row['last_out'])) : '-' ?></td>
                                <td><?= $row['late_minutes'] > 0 ? $row['late_minutes'] . ' min' : '-' ?></td>
                                <td><?= $row['early_leave_minutes'] > 0 ? $row['early_leave_minutes'] . ' min' : '-' ?></td>
                                <td><?= htmlspecialchars($row['presence_status']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include "../../../layouts/admin_layout_end.php"; ?>