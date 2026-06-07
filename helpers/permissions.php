<?php

function isEmployeeUser(): bool
{
    return has_role([
        'user',
        'manager',
        'hr',
        'it',
        'admin'
    ]);
}

function canApproveRequests(): bool
{
    return has_role([
        'manager',
        'admin'
    ]);
}

function canAccessOrganization(): bool
{
    return has_role([
        'hr',
        'admin'
    ]);
}

function canAccessEmployees(): bool
{
    return has_role([
        'hr',
        'admin'
    ]);
}

function canAccessAttendanceAdmin(): bool
{
    return has_role([
        'hr',
        'admin'
    ]);
}

function canAccessInventory(): bool
{
    return has_role([
        'it',
        'admin'
    ]);
}

function canAccessUsers(): bool
{
    return has_role([
        'it',
        'admin'
    ]);
}

function canAccessAudit(): bool
{
    return has_role([
        'admin'
    ]);
}
