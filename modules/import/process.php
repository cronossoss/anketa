<?php

require_once '../../config/init.php';

require_once '../../vendor/autoload.php';

require_once dirname(__DIR__) . '../assets/helpers/audit.php';

require_login();

require_role(['admin', 'it']);

$selectedRows =
    $_POST['selected_rows'] ?? [];

$importLimit =
    (int) ($_POST['import_limit'] ?? 100);

$importId =
    (int) ($_POST['import_id'] ?? 0);

if (!$importId) {

    die('Import nije pronađen.');
}

/* =========================
   IMPORT ROWS
========================= */

$ids =
    implode(
        ',',
        array_map(
            'intval',
            $selectedRows
        )
    );

if (!$ids) {

    die('Nema izabranih redova.');
}

$rowsResult = $conn->query("
    SELECT *
    FROM asset_import_rows
    WHERE id IN ({$ids})
    AND validation_status != 'error'
    ORDER BY row_number
    LIMIT {$importLimit}
");

$imported = 0;
$skipped = 0;
$failed = 0;

/* =========================
   HELPER
========================= */


function normalize_import_value($value)
{
    $value = trim($value);
    $value = mb_strtolower($value);

    $search = ['č', 'ć', 'š', 'ž', 'đ'];
    $replace = ['c', 'c', 's', 'z', 'dj'];

    $value = str_replace($search, $replace, $value);

    $value = preg_replace('/\s+/', ' ', $value);

    return $value;
}

function get_or_create_asset_type($conn, $typeName)
{
    if (!$typeName) {
        return null;
    }

    $normalized = normalize_import_value($typeName);

    $query = $conn->query("SELECT id, name FROM asset_types");

    while ($row = $query->fetch_assoc()) {

        if (
            normalize_import_value($row['name'])
            ===
            $normalized
        ) {
            return (int)$row['id'];
        }
    }

    $insert = $conn->prepare("
        INSERT INTO asset_types (
            name
        ) VALUES (?)
    ");

    $insert->bind_param(
        "s",
        $typeName
    );

    $insert->execute();

    return (int)$conn->insert_id;
}

function find_employee_id($conn, $fullName)
{
    if (!$fullName) {
        return null;
    }

    $normalizedSearch = normalize_import_value($fullName);

    $query = $conn->query("
        SELECT
            id,
            first_name,
            last_name
        FROM employees
    ");

    while ($employee = $query->fetch_assoc()) {

        $employeeName = normalize_import_value(
            $employee['first_name'] . ' ' . $employee['last_name']
        );

        if ($employeeName === $normalizedSearch) {
            return (int)$employee['id'];
        }
    }

    return null;
}



/* =========================
   PROCESS
========================= */

while ($row = $rowsResult->fetch_assoc()) {

/* echo '<pre>';
print_r($row);
die; */

    try {

        $inventoryNumber =
            trim($row['inventory_number']);

        if (!$inventoryNumber) {

            $failed++;
            continue;
        }

        /* =========================
           DUPLICATE CHECK
        ========================= */

        $inventoryEscaped =
            $conn->real_escape_string(
                $inventoryNumber
            );

        $existingResult =
            $conn->query("
                SELECT id
                FROM assets
                WHERE inventory_number = '{$inventoryEscaped}'
                LIMIT 1
            ");

        if ($existingResult->num_rows > 0) {

            $skipped++;

            $conn->query("
                UPDATE asset_import_rows
                SET validation_status = 'skipped'
                WHERE id = {$row['id']}
            ");

            continue;
        }

        /* =========================
           CATEGORY / TYPE
        ========================= */

        $rawData =
            json_decode(
                $row['raw_data'],
                true
            );

        $categoryName =
            trim(
                $row['category_name']
                ?: ($rawData['Kategorija uređaja'] ?? '')
            );

        $typeName =
            trim(
                $rawData['Tip uređaja'] ?? ''
            );

        $categoryId = null;
        $assetTypeId = null;

        if ($categoryName) {

            $categoryEscaped =
                $conn->real_escape_string(
                    $categoryName
                );

            $categoryResult =
                $conn->query("
                    SELECT
                        id,
                        asset_type_id
                    FROM asset_categories
                    WHERE name = '{$categoryEscaped}'
                    LIMIT 1
                ");

            if ($categoryResult->num_rows > 0) {

                $category =
                    $categoryResult->fetch_assoc();

                $categoryId =
                    (int) $category['id'];

                if (!empty($category['asset_type_id'])) {

                    $assetTypeId =
                        (int) $category['asset_type_id'];
                }
            }
        }

        /* =========================
           FALLBACK TYPE
        ========================= */

        if (!$assetTypeId) {

            $assetTypeId =
                get_or_create_asset_type(
                    $conn,
                    $typeName ?: 'Ostalo'
                );
        }

        if (!$assetTypeId) {

            throw new Exception(
                'Asset type nije pronađen.'
            );
        }

        /* =========================
           STATUS
        ========================= */

        $status = 'slobodno';

        if (!empty($row['employee_id'])) {

            $status = 'zaduzen';
        }

        /* =========================
           INSERT ASSET
        ========================= */

        $stmt = $conn->prepare("
            INSERT INTO assets (

                inventory_number,
                asset_type_id,
                category_id,

                manufacturer,
                model,
                serial_number,

                status

            )
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $manufacturer =
            trim($row['manufacturer']);

        $model =
            trim($row['model']);

        $serialNumber =
            trim($row['serial_number']);

        $stmt->bind_param(
            "siissss",

            $inventoryNumber,
            $assetTypeId,
            $categoryId,

            $manufacturer,
            $model,
            $serialNumber,

            $status
        );

        $stmt->execute();

        $assetId =
            $conn->insert_id;

        /* =========================
           ASSIGNMENT
        ========================= */

        if (!empty($row['employee_id'])) {

            $employeeId =
                (int) $row['employee_id'];

            $assignedBy =
                (int) $_SESSION['user_id'];

            $assignStmt = $conn->prepare("
                INSERT INTO asset_assignments (

                    asset_id,
                    employee_id,
                    assigned_by

                )
                VALUES (?, ?, ?)
            ");

            $assignStmt->bind_param(
                "iii",
                $assetId,
                $employeeId,
                $assignedBy
            );

            $assignStmt->execute();
        }

        /* =========================
           IMPORT STATUS
        ========================= */

        $conn->query("
            UPDATE asset_import_rows
            SET validation_status = 'imported'
            WHERE id = {$row['id']}
        ");

        $imported++;

    } catch (Exception $e) {

        $failed++;

        $messageEscaped =
            $conn->real_escape_string(
                $e->getMessage()
            );

        $conn->query("
            UPDATE asset_import_rows
            SET
                validation_status = 'error',
                validation_message = '{$messageEscaped}'
            WHERE id = {$row['id']}
        ");
    }
}

/* =========================
   FINALIZE IMPORT
========================= */

$conn->query("
    UPDATE asset_imports
    SET

        imported_rows = {$imported},
        skipped_rows = {$skipped},
        failed_rows = {$failed},

        status = 'completed',

        completed_at = NOW()

    WHERE id = {$importId}
");

/* =========================
   SUCCESS
========================= */

$_SESSION['success'] =
    'Import završen. '
    . 'Uvezeno: '
    . $imported
    . ', preskočeno: '
    . $skipped
    . ', greške: '
    . $failed;

header(
    "Location: preview.php?id={$importId}"
);

exit;