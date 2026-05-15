<?php

require_once $_SERVER['DOCUMENT_ROOT']
    . '/anketa/config/init.php';
require_once '../helpers/permissions.php';
require_once '../helpers/audit.php';

require_login();

require_role(['admin', 'it']);
$result = $conn->query("
    SELECT *
    FROM asset_types
    ORDER BY name
");

$types = [];

while ($row = $result->fetch_assoc()) {
    $types[] = $row;
}

$result = $conn->query("
    SELECT *
    FROM asset_categories
    ORDER BY name
");

$categories = [];

while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}
?>

<form
    method="POST"
    action="../actions/assets_create.php"
>

    <div class="form-group">
        <label>Inventarski broj</label>

        <input
            type="text"
            name="inventory_number"
            required
        >
    </div>

    <div class="form-group">
        <label>Naziv</label>

        <input
            type="text"
            name="name"
            required
        >
    </div>

    <div class="form-group">
        <label>Tip</label>

        <select name="asset_type_id" required>

            <option value="">
                Izaberite
            </option>

            <?php foreach ($types as $type): ?>

                <option value="<?= $type['id'] ?>">
                    <?= htmlspecialchars($type['name']) ?>
                </option>

            <?php endforeach; ?>

        </select>
    </div>

    <div class="form-group">
        <label>Kategorija</label>

        <select name="category_id" required>

            <option value="">
                Izaberite
            </option>

            <?php foreach ($categories as $category): ?>

                <option value="<?= $category['id'] ?>">
                    <?= htmlspecialchars($category['name']) ?>
                </option>

            <?php endforeach; ?>

        </select>
    </div>

    <button type="submit">
        Sačuvaj
    </button>

</form>