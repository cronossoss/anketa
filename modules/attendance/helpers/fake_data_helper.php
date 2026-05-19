<?php

function generateRandomTime(
    $baseTime,
    $variationMinutes = 15
) {

    $timestamp = strtotime($baseTime);

    $variation = rand(
        -$variationMinutes,
        $variationMinutes
    );

    return date(
        'H:i:s',
        strtotime("$variation minutes", $timestamp)
    );
}

function generateFakeScenario()
{
    $scenarios = [
        'normal',
        'late',
        'early_leave',
        'missing_out',
        'overtime',
        'absent'
    ];

    $weights = [
        70,
        10,
        5,
        5,
        5,
        5
    ];

    $rand = rand(1, array_sum($weights));

    $current = 0;

    foreach ($scenarios as $index => $scenario) {

        $current += $weights[$index];

        if ($rand <= $current) {
            return $scenario;
        }
    }

    return 'normal';
}