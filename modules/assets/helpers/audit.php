<?php

function logAudit(
    mysqli $conn,
    ?int $userId,
    string $module,
    string $action,
    ?int $targetId,
    ?string $description
): void {

    $stmt = $conn->prepare("
        INSERT INTO audit_logs (
            user_id,
            module,
            action,
            target_id,
            description,
            ip_address
        )
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $ip =
        $_SERVER['REMOTE_ADDR']
        ?? null;

    $stmt->bind_param(
        'ississ',
        $userId,
        $module,
        $action,
        $targetId,
        $description,
        $ip
    );

    $stmt->execute();
}