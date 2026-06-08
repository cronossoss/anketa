<?php require_once __DIR__ . '/navigation.php'; ?>

<div
    class="offcanvas offcanvas-start"
    tabindex="-1"
    id="mobileMenu">

    <div class="offcanvas-header bg-primary text-white">

        <div>
            <h4 class="mb-0">IIS</h4>
            <small>Integrisani informacioni sistem</small>
        </div>

        <button
            type="button"
            class="btn-close btn-close-white"
            data-bs-dismiss="offcanvas">
        </button>

    </div>

    <div class="offcanvas-body bg-primary text-white">

        <ul class="nav flex-column">

            <?php foreach ($menuSections as $section => $items): ?>

                <?php if (empty($items)) continue; ?>

                <li class="nav-item mt-3 mb-2">

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

                        </a>

                    </li>

                <?php endforeach; ?>

            <?php endforeach; ?>

            <li class="nav-item mt-3">

                <a
                    class="nav-link text-white"
                    href="<?= url('logout.php') ?>">

                    <i class="bi bi-box-arrow-right me-2"></i>

                    Logout

                </a>

            </li>

        </ul>

    </div>

</div>