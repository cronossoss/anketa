<?php

function audit_log(
    $module,
    $action,
    $targetId = null,
    $description = null
) {

    global $conn;

    $userId =
        $_SESSION['user_id']
        ?? null;

    $ip =
        $_SERVER['REMOTE_ADDR']
        ?? null;

    $stmt = $conn->prepare("
        INSERT INTO audit_logs
        (
            user_id,
            module,
            action,
            target_id,
            description,
            ip_address
        )
        VALUES
        (?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "ississ",
        $userId,
        $module,
        $action,
        $targetId,
        $description,
        $ip
    );

    $stmt->execute();
}
