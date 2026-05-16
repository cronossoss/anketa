<?php

function e($string)
{
    return htmlspecialchars(
        $string ?? '',
        ENT_QUOTES,
        'UTF-8'
    );
}


function redirect($url)
{
    header("Location: " . $url);
    exit;
}

function pluralize(
    $number,
    $one,
    $few,
    $many
) {

    $n = abs((int)$number);

    if (
        $n % 10 == 1 &&
        $n % 100 != 11
    ) {
        return $one;
    }

    if (
        $n % 10 >= 2 &&
        $n % 10 <= 4 &&
        (
            $n % 100 < 10 ||
            $n % 100 >= 20
        )
    ) {
        return $few;
    }

    return $many;
}

function dump($data)
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

function dd($data)
{
    dump($data);
    die();
}
