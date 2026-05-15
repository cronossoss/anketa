<?php

require_once '../../config/init.php';

require_once '../../vendor/autoload.php';


use PhpOffice\PhpSpreadsheet\IOFactory;

require_login();

require_role(['admin', 'it']);

if (!isset($_FILES['excel_file'])) {

    die('Fajl nije poslat.');
}

$tmpFile =
    $_FILES['excel_file']['tmp_name'];

$fileName =
    $_FILES['excel_file']['name'];

$spreadsheet =
    IOFactory::load($tmpFile);

$sheet =
    $spreadsheet->getActiveSheet();

$rows =
    $sheet->toArray();

if (!$rows || count($rows) < 2) {

    die('Excel nema podatke.');
}

/* =========================
   IMPORT SESSION
========================= */

$stmt = $conn->prepare("
    INSERT INTO asset_imports (
        imported_by,
        file_name,
        total_rows
    )
    VALUES (?, ?, ?)
");

$totalRows =
    count($rows) - 1;

$stmt->bind_param(
    "isi",
    $_SESSION['user_id'],
    $fileName,
    $totalRows
);

$stmt->execute();

$importId =
    $conn->insert_id;

/* =========================
   HEADERS
========================= */

$headers =
    array_map(
        'trim',
        $rows[0]
    );

unset($rows[0]);

/* =========================
   PROCESS ROWS
========================= */

foreach ($rows as $index => $row) {

    $data =
        array_combine(
            $headers,
            $row
        );

    $inventoryNumber =
        trim($data['Šifra uređaja'] ?? '');

    $manufacturer =
        trim($data['Proizvođač'] ?? '');

    $model =
        trim($data['Model'] ?? '');

    $serialNumber =
        trim($data['Serijski broj'] ?? '');

    $employeePersonalNumber =
        trim($data['Šifra zaposlenog'] ?? '');

    $employeeName =
        trim($data['Ime i prezime'] ?? '');

    $categoryName =
        trim($data['Kategorija'] ?? '');

    $validationStatus =
        'valid';

    $validationMessage =
        '';

    /* =========================
       EMPLOYEE CHECK
    ========================= */

    $employeeId = null;

    if ($employeePersonalNumber) {

        $employeeEscaped =
            $conn->real_escape_string(
                $employeePersonalNumber
            );

        $employeeResult =
            $conn->query("
                SELECT id
                FROM employees
                WHERE personal_id = '{$employeeEscaped}'
                LIMIT 1
            ");

        if ($employeeResult->num_rows > 0) {

            $employee =
                $employeeResult
                ->fetch_assoc();

            $employeeId =
                $employee['id'];

        } else {

            $validationStatus =
                'warning';

            $validationMessage =
                'Radnik nije pronađen.';
        }
    }

    /* =========================
       DUPLICATE ASSET
    ========================= */

    if ($inventoryNumber) {

        $inventoryEscaped =
            $conn->real_escape_string(
                $inventoryNumber
            );

        $assetResult =
            $conn->query("
                SELECT id
                FROM assets
                WHERE inventory_number = '{$inventoryEscaped}'
                LIMIT 1
            ");

        if ($assetResult->num_rows > 0) {

            $validationStatus =
                'error';

            $validationMessage =
                'Inventarski broj već postoji.';
        }
    }

    /* =========================
       SAVE IMPORT ROW
    ========================= */

    $stmt = $conn->prepare("
        INSERT INTO asset_import_rows (

            import_id,
            row_number,

            inventory_number,
            manufacturer,
            model,
            serial_number,

            employee_personal_number,
            employee_name,

            category_name,

            raw_data,

            validation_status,
            validation_message,

            employee_id

        )
        VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )
    ");

    $rawJson =
        json_encode(
            $data,
            JSON_UNESCAPED_UNICODE
        );

    $rowNumber =
        $index + 2;

    $stmt->bind_param(
        "iissssssssssi",

        $importId,
        $rowNumber,

        $inventoryNumber,
        $manufacturer,
        $model,
        $serialNumber,

        $employeePersonalNumber,
        $employeeName,

        $categoryName,

        $rawJson,

        $validationStatus,
        $validationMessage,

        $employeeId
    );

    $stmt->execute();
}

header(
    "Location: preview.php?id={$importId}"
);

exit;