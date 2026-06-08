<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/navigation.php';

?>

<div class="sidebar bg-primary text-white p-3 d-none d-lg-block">

    <div class="app-brand">

        <div class="app-brand-short">
            IIS
        </div>

        <div class="app-brand-full">
            Integrisani informacioni sistem
        </div>

    </div>

    <ul class="nav flex-column">

        <?php foreach ($menuSections as $section => $items): ?>

            <?php if (empty($items)) continue; ?>

            <li class="nav-item mt-2 mb-1">

                <small
                    class="text-white-50 fw-bold text-uppercase">

                    <?= e($section) ?>

                </small>

            </li>

            <?php foreach ($items as $item): ?>

                <li class="nav-item mb-1">

                    <a
                        href="<?= $item['url'] ?>"
                        class="nav-link text-white <?= isActive($item['page']) ?>">

                        <?php if (!empty($item['icon'])): ?>

                            <i class="<?= e($item['icon']) ?> me-2"></i>

                        <?php endif; ?>

                        <?= e($item['title']) ?>

                        <?php if (!empty($item['counter'])): ?>

                            <span class="badge bg-danger ms-2">

                                <?= (int)$item['counter'] ?>

                            </span>

                        <?php endif; ?>

                    </a>

                </li>

            <?php endforeach; ?>

        <?php endforeach; ?>

        <li class="nav-item mt-4">

            <a
                class="nav-link text-white"
                href="<?= url('logout.php') ?>">

                <i class="bi bi-box-arrow-right me-2"></i>

                Logout

            </a>

        </li>

    </ul>

</div>