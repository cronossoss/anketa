<?php

$pageTitle = "Pregled inventara";

$currentPage = 'asset-items';

include "../../../layouts/admin_layout_start.php";

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

$stmt = $conn->prepare("
    SELECT
        a.*,

        t.name AS type_name,

        c.name AS category_name

    FROM assets a

    LEFT JOIN asset_types t
        ON t.id = a.asset_type_id

    LEFT JOIN asset_categories c
        ON c.id = a.category_id

    WHERE a.id = ?
");

$stmt->bind_param(
    "i",
    $id
);

$stmt->execute();

$asset =
    $stmt
    ->get_result()
    ->fetch_assoc();

if (!$asset) {
    die('Asset nije pronađen.');
}
?>

<main class="main-content">


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



        <?php

        $stmtAttr = $conn->prepare("
            SELECT
                d.name,
                d.field_type,
                v.value_text
            FROM asset_attribute_values v

            LEFT JOIN asset_attribute_definitions d
                ON d.id = v.attribute_definition_id

            WHERE v.asset_id = ?

            ORDER BY d.sort_order, d.name
        ");
        $stmtAttr->bind_param(
            "i",
            $id
        );

        $stmtAttr->execute();

        $attributes =
            $stmtAttr
            ->get_result();
        ?>

        <?php if ($attributes->num_rows > 0): ?>

            <div class="card mt-4">

                <h3>

                    Dodatni atributi

                </h3>

                <table class="table">

                    <?php while ($attr = $attributes->fetch_assoc()): ?>

                        <tr>

                            <th width="250">

                                <?= htmlspecialchars(
                                    $attr['name']
                                ) ?>

                            </th>

                            <td>

                                <?=
                                nl2br(
                                    htmlspecialchars(
                                        $attr['value_text']
                                    )
                                )
                                ?>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </table>

            </div>

        <?php endif; ?>

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


</main>


<?php include "../../../layouts/footer.php"; ?>