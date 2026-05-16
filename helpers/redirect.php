<?php

if (!function_exists('redirect')) {

    function redirect($path)
    {
        header(
            'Location: ' . url($path)
        );

        exit;
    }
}
