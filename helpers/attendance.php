<?php

function calculate_overtime_hours(
    int $minutes
): int {

    if ($minutes < 45) {

        return 0;
    }

    return floor(
        ($minutes - 45) / 60
    ) + 1;
}