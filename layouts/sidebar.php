<?php

$sidebarItems = require
    __DIR__ . '/../../config/sidebar.php';
?>

<aside class="sidebar">

    <div class="sidebar-brand">
        HK "Krušik"
    </div>

    <ul class="sidebar-menu list-unstyled">

        <?php foreach ($sidebarItems as $item): ?>

            <?php if (!has_role($item['roles'])) continue; ?>

            <?php
            $isActive = str_contains(
                $_SERVER['REQUEST_URI'],
                $item['url']
            );
            ?>

            <li class="sidebar-item <?= $isActive ? 'active' : '' ?>">

                <a
                    href="<?= BASE_URL . $item['url'] ?>"
                    class="sidebar-link">

                    <i class="<?= e($item['icon']) ?>"></i>

                    <span>
                        <?= e($item['title']) ?>
                    </span>

                </a>

            </li>

        <?php endforeach; ?>

    </ul>

</aside>