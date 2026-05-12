<?php

require_once '../../config/init.php';

require_login();
require_admin();

$unitId = (int)($_GET['unit_id'] ?? 0);

$stmt = $conn->prepare("
    SELECT
        id,
        first_name,
        last_name,
        position,
        is_manager
    FROM employees
    WHERE organizational_unit_id = ?
    ORDER BY last_name
");

$stmt->bind_param('i', $unitId);

$stmt->execute();

$result = $stmt->get_result();

$employees = [];

while ($row = $result->fetch_assoc()) {
    $employees[] = $row;
}

?>

<?php if (empty($employees)): ?>

    <div class="text-muted">
        Nema zaposlenih.
    </div>

<?php else: ?>

    <div class="table-responsive">

        <table class="table table-hover align-middle">

            <thead>

                <tr>
                    <th>Ime i prezime</th>
                    <th>Pozicija</th>
                </tr>

            </thead>

            <tbody>

                <?php foreach ($employees as $employee): ?>

                    <tr class="employee-row" data-id="<?= $employee['id'] ?>">

                        <td>
                            <?= e($employee['first_name']) ?>
                            <?= e($employee['last_name']) ?>
                        </td>

                        <td>

                            <div class="d-flex align-items-center gap-2">

                                <span>
                                    <?= e($employee['position']) ?>
                                </span>

                                <?php if ($employee['is_manager']): ?>

                                    <span class="badge bg-primary">
                                        Rukovodilac
                                    </span>

                                <?php endif; ?>

                            </div>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    </div>

<?php endif; ?>