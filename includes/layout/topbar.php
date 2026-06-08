<?php

$userName =
    $_SESSION['name'] ?? 'Korisnik';

$userPhoto =
    $_SESSION['photo'] ?? null;

$stmt = $conn->prepare("
    SELECT
        e.first_name,
        e.last_name,
        ou.name AS organizational_unit
    FROM users u
    JOIN employees e
        ON e.id = u.employee_id
    LEFT JOIN organizational_units ou
        ON ou.id = e.organizational_unit_id
    WHERE u.id = ?
    LIMIT 1
");

$stmt->bind_param(
    'i',
    $_SESSION['user_id']
);

$stmt->execute();

$userData =
    $stmt
    ->get_result()
    ->fetch_assoc();

$userRole =
    ucfirst(
        $_SESSION['role'] ?? ''
    );

$userName =
    $_SESSION['name'] ?? 'Korisnik';

$userPhoto =
    $_SESSION['photo'] ?? null;

$announcementText =
    $announcementText ?? '';

$announcementText =
    'Kolektivni godišnji odmor od 01.08.2026. do 15.08.2026.';

?>
<div class="topbar-left">

    <button
        class="btn d-lg-none me-3"
        data-bs-toggle="offcanvas"
        data-bs-target="#mobileMenu">

        <i class="bi bi-list fs-4"></i>

    </button>

    <h4 class="page-title mb-0">

        <?= e($pageTitle ?? '') ?>

    </h4>

</div>

<div class="topbar">

    <div class="topbar-left">

        <h4 class="page-title">
            <?= e($pageTitle) ?>
        </h4>

        <?php if (!empty($announcementText)): ?>

            <div class="topbar-announcements">

                <div class="announcement-track">

                    <?= e($announcementText) ?>

                </div>

            </div>

        <?php endif; ?>

    </div>

    <div class="topbar-right">

        <div class="user-info">

            <div class="user-name">

                <?= e($userName) ?>

            </div>

            <div class="user-meta">

                <?= e(
                    $userData['organizational_unit']
                        ?? ''
                ) ?>

            </div>

            <div class="user-role">

                <?= e($userRole) ?>

            </div>

        </div>

        <?php if ($userPhoto): ?>

            <img
                src="<?= url('uploads/employees/' . $userPhoto) ?>"
                class="topbar-avatar"
                alt="Avatar">

        <?php endif; ?>

    </div>

</div>

<style>
    @keyframes ticker {

    from {
        transform: translateX(100%);
    }

    to {
        transform: translateX(-100%);
    }

}

</style>

