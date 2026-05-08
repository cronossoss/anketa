<?php
require_once "../config/db.php";
require_login();

$search = $_GET['search'] ?? '';
$manager = $_GET['manager'] ?? '';

$sql = "
SELECT 
    e.*,
    CONCAT(e.first_name, ' ', e.last_name) as name,
    o.name as unit_name
FROM employees e
LEFT JOIN organizational_units o ON o.id = e.organizational_unit_id
WHERE 1
";

$params = [];
$types = "";

// SEARCH
if ($search !== '') {
    $sql .= " AND (
        e.first_name LIKE ? OR
        e.last_name LIKE ? OR
        e.personal_id LIKE ?
    )";
    $like = "%$search%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= "sss";
}

// FILTER
if ($manager !== '') {
    $sql .= " AND e.is_manager = ?";
    $params[] = (int)$manager;
    $types .= "i";
}

$sql .= " ORDER BY e.first_name";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

while ($e = $result->fetch_assoc()):
?>
    <tr class="emp-row"
        data-name="<?= e($e['name']) ?>"
        data-email="<?= e($e['email']) ?>"
        data-personal="<?= e($e['personal_id']) ?>"
        data-jmbg="<?= e($e['jmbg']) ?>"
        data-unit="<?= e($e['unit_name']) ?>"
        data-birth="<?= e($e['birth_date'] ?? '') ?>"
        data-pos="<?= e($e['position']) ?>"
        data-hire="<?= e($e['hire_date']) ?>"
        data-end="<?= e($e['contract_end'] ?? '') ?>"
        data-contract="<?= e($e['contract_type']) ?>"
        data-manager="<?= (int)$e['is_manager'] ?>"
        data-photo="<?= e($e['photo']) ?>"
        style="cursor:pointer">
        <td><?= e($e['name']) ?></td>
        <td><?= e($e['personal_id']) ?></td>
        <td><?= e($e['unit_name']) ?></td>
        <td><?= $e['is_manager'] ? '<span class="badge bg-primary">DA</span>' : '-' ?></td>
    </tr>
<?php endwhile; ?>