<?php
require_once "../config/db.php";
require_login();
require_admin();

include "../layout/header.php";
include "../layout/sidebar.php";

// lista svih korisnika + OJ + status ankete
$sql = "
SELECT 
    u.id as user_id,
    e.first_name,
    e.last_name,
    o.name as org,
    s.id as survey_id,
    s.status
FROM users u
LEFT JOIN employees e ON e.id = u.employee_id
LEFT JOIN organizational_units o ON o.id = e.organizational_unit_id
LEFT JOIN surveys s ON s.user_id = u.id
WHERE u.role = 'user'
ORDER BY o.name
";

$result = $conn->query($sql);
?>

<main class="col-lg-10 main-content ms-auto">

<h3>Pregled anketa</h3>

<table class="table table-bordered">
    <tr>
        <th>Organizaciona jedinica</th>
        <th>Korisnik</th>
        <th>Status</th>
        <th>Akcija</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= e($row['org']) ?></td>
            <td><?= e($row['first_name'] . ' ' . $row['last_name']) ?></td>

            <td>
                <?php if ($row['status'] === 'submitted'): ?>
                    <span class="badge bg-success">Popunjeno</span>
                <?php else: ?>
                    <span class="badge bg-danger">Nije popunjeno</span>
                <?php endif; ?>
            </td>

            <td>
                <?php if ($row['survey_id']): ?>
                    <a href="admin/view_survey.php?id=<?= $row['survey_id'] ?>" class="btn btn-sm btn-primary">
                        Pregled
                    </a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>

</table>

</main>

<?php include "../layout/footer.php"; ?>