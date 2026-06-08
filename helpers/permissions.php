<?php

function hasPermission(
    string $permission
): bool {

    global $conn;

    if (!isset($_SESSION['user_id'])) {

        return false;
    }

    if (has_role('admin')) {

        return true;
    }

    static $permissions = null;

    if ($permissions === null) {

        $permissions = [];

        $stmt = $conn->prepare("
            SELECT permission
            FROM user_permissions
            WHERE user_id = ?
        ");

        $stmt->bind_param(
            'i',
            $_SESSION['user_id']
        );

        $stmt->execute();

        $result =
            $stmt->get_result();

        while (
            $row =
                $result->fetch_assoc()
        ) {

            $permissions[] =
                $row['permission'];
        }
    }

    return in_array(
        $permission,
        $permissions,
        true
    );
}

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
    return hasPermission(
        'inventory'
    );
}

function canAccessUsers(): bool
{
    return has_role([
        'admin'
    ]);
}

function canAccessAudit(): bool
{
    return has_role([
        'admin'
    ]);
}
