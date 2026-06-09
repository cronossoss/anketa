<?php

function audit_log(
    string $module,
    string $action,
    ?int $targetId = null,
    ?string $description = null
): void {

    global $conn;

    $userId =
        $_SESSION['user_id']
        ?? null;

    $ip =
        $_SERVER['REMOTE_ADDR']
        ?? null;

    $userAgent =
        $_SERVER['HTTP_USER_AGENT']
        ?? null;

    $stmt = $conn->prepare("
        INSERT INTO audit_logs
        (
            user_id,
            module,
            action,
            target_id,
            description,
            ip_address,
            user_agent
        )
        VALUES
        (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "ississs",
        $userId,
        $module,
        $action,
        $targetId,
        $description,
        $ip,
        $userAgent
    );

    $stmt->execute();
}
