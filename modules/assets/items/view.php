<?php

require_once $_SERVER['DOCUMENT_ROOT']
    . '/anketa/config/init.php';

require_Login();

if (!hasRole(['admin', 'it'])) {
    die('Nemate dozvolu.');
}

$id = (int) ($_GET['id'] ?? 0);

if (!$id) {
    die('Neispravan ID.');
}

$asset = getAssetById(
    $conn,
    $id
);

if (!$asset) {
    die('Asset nije pronađen.');
}
?>

<div class="page-header">

    <h1>
        <?= htmlspecialchars($asset['name']) ?>
    </h1>

</div>

<div class="card">

    <h3>Osnovni podaci</h3>

    <table class="table">

        <tr>
            <th>Inventarski broj</th>

            <td>
                <?= htmlspecialchars($asset['inventory_number']) ?>
            </td>
        </tr>

        <tr>
            <th>Tip</th>

            <td>
                <?= htmlspecialchars($asset['type_name']) ?>
            </td>
        </tr>

        <tr>
            <th>Kategorija</th>

            <td>
                <?= htmlspecialchars($asset['category_name']) ?>
            </td>
        </tr>

        <tr>
            <th>Status</th>

            <td>
                <?= htmlspecialchars($asset['status']) ?>
            </td>
        </tr>

        <tr>
            <th>Lokacija</th>

            <td>
                <?= htmlspecialchars($asset['location']) ?>
            </td>
        </tr>

    </table>

</div>

<div class="card">

    <h3>Tehnički podaci</h3>

    <table class="table">

        <tr>
            <th>Proizvođač</th>

            <td>
                <?= htmlspecialchars(
                    $asset['manufacturer']
                    ?? '-'
                ) ?>
            </td>
        </tr>

        <tr>
            <th>Model</th>

            <td>
                <?= htmlspecialchars(
                    $asset['model']
                    ?? '-'
                ) ?>
            </td>
        </tr>

        <tr>
            <th>Serijski broj</th>

            <td>
                <?= htmlspecialchars(
                    $asset['serial_number']
                    ?? '-'
                ) ?>
            </td>
        </tr>

    </table>

</div>

<div class="card">

    <h3>Finansije</h3>

    <table class="table">

        <tr>
            <th>Datum kupovine</th>

            <td>
                <?= htmlspecialchars(
                    $asset['purchase_date']
                    ?? '-'
                ) ?>
            </td>
        </tr>

        <tr>
            <th>Garancija do</th>

            <td>
                <?= htmlspecialchars(
                    $asset['warranty_until']
                    ?? '-'
                ) ?>
            </td>
        </tr>

        <tr>
            <th>Dobavljač</th>

            <td>
                <?= htmlspecialchars(
                    $asset['supplier']
                    ?? '-'
                ) ?>
            </td>
        </tr>

        <tr>
            <th>Cena</th>

            <td>
                <?= htmlspecialchars(
                    $asset['unit_price']
                    ?? '-'
                ) ?>
            </td>
        </tr>

    </table>

</div>