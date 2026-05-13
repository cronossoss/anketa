<?php

function hasRole(array $roles): bool
{
    if (
        !isset($_SESSION['role'])
    ) {
        return false;
    }

    return in_array(
        $_SESSION['role'],
        $roles
    );
}