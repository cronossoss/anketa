<?php

$pageTitle = "Inventar";

include "../../../layouts/admin_layout_start.php";


require_once $_SERVER['DOCUMENT_ROOT']
    . '/anketa/config/init.php';
require_once '../helpers/permissions.php';
require_once '../helpers/audit.php';

require_Login();

if (!hasRole(['admin', 'it'])) {
    die('Nemate dozvolu.');
}

$result = $conn->query("
    SELECT
        a.id,
        a.inventory_number,
        a.name,
        a.status,

        c.name AS category_name,

        CONCAT(
            e.first_name,
            ' ',
            e.last_name
        ) AS employee_name,

        ou.name AS organizational_unit_name

    FROM assets a

    LEFT JOIN asset_categories c
        ON c.id = a.category_id

    LEFT JOIN asset_assignments aa
        ON aa.asset_id = a.id
        AND aa.returned_at IS NULL

    LEFT JOIN employees e
        ON e.id = aa.employee_id

    LEFT JOIN organizational_units ou
        ON ou.id = e.organizational_unit_id

    ORDER BY a.created_at DESC
");

$assets = [];

while ($row = $result->fetch_assoc()) {
    $assets[] = $row;
}
?>

<?php if (empty($assets)): ?>

    <div class="empty-state">

        Nema unetih uređaja.

    </div>

<?php endif; ?>

<div class="page-header">
    <h1>Assets</h1>

    <a href="create.php" class="btn btn-primary">
        Novi uređaj
    </a>
</div>

<table class="table">
    <thead>
        <tr>
            <th>Inventarski broj</th>
            <th>Naziv</th>
            <th>Tip</th>
            <th>Kategorija</th>
            <th>Status</th>
            <th>Lokacija</th>
            <th></th>
        </tr>
    </thead>

    <tbody>

    <?php foreach ($assets as $asset): ?>

        <tr
            class="asset-row"
            data-id="<?= $asset['id'] ?>"
        >

            <td>
                <?= htmlspecialchars(
                    $asset['inventory_number']
                ) ?>
            </td>

            <td>

                <a
                    href="#"
                    class="asset-view-btn"
                    data-id="<?= $asset['id'] ?>"
                >
                    <?= htmlspecialchars(
                        $asset['name']
                    ) ?>
                </a>

            </td>

            <td>
                <?= htmlspecialchars(
                    $asset['category_name']
                ) ?>
            </td>

            <td>
                <?= htmlspecialchars(
                    $asset['employee_name']
                    ?? '-'
                ) ?>
            </td>

            <td>
                <?= htmlspecialchars(
                    $asset['organizational_unit_name']
                    ?? '-'
                ) ?>
            </td>

        </tr>

    <?php endforeach; ?>

    </tbody>
</table>