<?php

function url($path = '')
{
    return rtrim(APP_URL, '/')
        . '/'
        . ltrim($path, '/');
}
