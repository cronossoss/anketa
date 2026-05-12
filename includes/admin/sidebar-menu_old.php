<ul class="nav flex-column sidebar-nav">

    <li class="nav-item">

        <a class="nav-link text-white <?= isActive('/admin/dashboard.php') ?>"
            href="<?= BASE_URL ?>admin/dashboard.php">

            <i class="bi bi-speedometer2 me-2"></i>
            Dashboard

        </a>

    </li>

    <li class="nav-item">

        <a class="nav-link text-white <?= isActive('/admin/organization.php') ?>"
            href="<?= BASE_URL ?>admin/organization.php">

            <i class="bi bi-people me-2"></i>
            Organizacija

        </a>

    </li>

    <li class="nav-item">

        <a class="nav-link text-white <?= isActive('/admin/employees.php') ?>"
            href="<?= BASE_URL ?>admin/employees.php">

            <i class="bi bi-people me-2"></i>
            Zaposleni

        </a>

    </li>

    <li class="nav-item">

        <a class="nav-link text-white <?= isActive('/admin/users.php') ?>"
            href="<?= BASE_URL ?>admin/users.php">

            <i class="bi bi-person-badge me-2"></i>
            Korisnici

        </a>

    </li>

    <li class="nav-item mt-3">

        <a class="nav-link text-white"
            href="<?= BASE_URL ?>logout.php">

            <i class="bi bi-box-arrow-right me-2"></i>
            Logout

        </a>

    </li>

</ul>