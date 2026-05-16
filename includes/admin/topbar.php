<?php

$name = trim($_SESSION['name'] ?? 'User');

$nameParts = explode(' ', $name);

$initials = '';

foreach ($nameParts as $part) {

    if (!empty($part)) {
        $initials .= strtoupper(substr($part, 0, 1));
    }
}

$photoPath =
    __DIR__ .
    "/../../uploads/employees/" .
    ($_SESSION['photo'] ?? '');

$hasPhoto =
    !empty($_SESSION['photo']) &&
    file_exists($photoPath);

?>

<div class="topbar bg-white shadow-sm px-4 py-3 d-flex justify-content-between align-items-center">

    <div class="d-flex align-items-center gap-3 flex-grow-1">

        <div class="flex-grow-1 text-center">

            <div class="fw-bold fs-5">

                <?= $pageTitle ?? 'Admin panel' ?>

            </div>

        </div>

    </div>

    <div class="d-flex align-items-center gap-3">

        <div>
            <?= e($name) ?>
        </div>

        <?php if ($hasPhoto): ?>

            <img
                src="<?= url(
                            'uploads/employees/' .
                                $_SESSION['photo']
                        ) ?>"
                width="40"
                height="40"
                class="rounded-circle object-fit-cover">

        <?php else: ?>

            <div class="user-avatar">
                <?= $initials ?: 'U' ?>
            </div>

        <?php endif; ?>

    </div>

</div>