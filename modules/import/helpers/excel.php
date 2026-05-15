<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

function loadExcelRows(string $filePath): array
{
    $spreadsheet =
        IOFactory::load($filePath);

    $sheet =
        $spreadsheet->getActiveSheet();

    return $sheet->toArray();
}