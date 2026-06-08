<div class="container-fluid">

    <div class="mb-4">

        <div class="alert alert-light border">
            Dobrodošli u integrisani informacioni sistem - IIS
        </div>

    </div>

    <!-- KADROVI -->

    <h5 class="mb-3 text-uppercase text-muted">
        Kadrovi
    </h5>

    <div class="row g-3 mb-4">

        <div class="col-md-6 col-xl-3">

            <div class="dashboard-card">

                <div class="dashboard-card-header">

                    <div class="dashboard-card-title">

                        <i class="bi bi-diagram-3"></i>

                        <span>Organizacija</span>

                    </div>

                    <a href="<?= url('admin/organization.php') ?>"
                        class="btn btn-sm btn-primary">
                        Otvori
                    </a>

                </div>

                <div class="dashboard-card-description">

                    Organizacione jedinice i hijerarhija

                </div>

            </div>

        </div>

        <div class="col-md-6 col-xl-3">

            <div class="dashboard-card">

                <div class="dashboard-card-header">

                    <div class="dashboard-card-title">

                        <i class="bi bi-people"></i>

                        <span>Zaposleni</span>

                    </div>

                    <a href="<?= url('admin/employees.php') ?>"
                        class="btn btn-sm btn-primary">
                        Otvori
                    </a>

                </div>

                <div class="dashboard-card-description">

                    Evidencija zaposlenih

                </div>

            </div>

        </div>

    </div>

    <!-- PRISUSTVO -->

    <h5 class="mb-3 text-uppercase text-muted">
        Prisustvo
    </h5>

    <div class="row g-3 mb-4">

        <div class="col-md-6 col-xl-3">

            <div class="dashboard-card">

                <div class="dashboard-card-header">

                    <div class="dashboard-card-title">

                        <i class="bi bi-calendar-check"></i>

                        <span>Prisustvo</span>

                    </div>

                    <a href="<?= url('modules/attendance/present.php') ?>"
                        class="btn btn-sm btn-primary">
                        Otvori
                    </a>

                </div>

                <div class="dashboard-card-description">

                    Trenutno stanje zaposlenih

                </div>

            </div>

        </div>

        <div class="col-md-6 col-xl-3">

            <div class="dashboard-card">

                <div class="dashboard-card-header">

                    <div class="dashboard-card-title">

                        <i class="bi bi-calendar-x"></i>

                        <span>Odsustva</span>

                    </div>

                    <a href="<?= url('modules/attendance/absences/index.php') ?>"
                        class="btn btn-sm btn-primary">
                        Otvori
                    </a>

                </div>

                <div class="dashboard-card-description">

                    Upravljanje odsustvima

                </div>

            </div>

        </div>

        <div class="col-md-6 col-xl-3">

            <div class="dashboard-card">

                <div class="dashboard-card-header">

                    <div class="dashboard-card-title">

                        <i class="bi bi-door-open"></i>

                        <span>Izlaznice</span>

                    </div>

                    <a href="<?= url('modules/attendance/exit_passes/index.php') ?>"
                        class="btn btn-sm btn-primary">
                        Otvori
                    </a>

                </div>

                <div class="dashboard-card-description">

                    Evidencija izlaznica

                </div>

            </div>

        </div>

    </div>

    <!-- IT INVENTAR -->

    <h5 class="mb-3 text-uppercase text-muted">
        IT Inventar
    </h5>

    <div class="row g-3 mb-4">

        <div class="col-md-6 col-xl-3">

            <div class="dashboard-card">

                <div class="dashboard-card-header">

                    <div class="dashboard-card-title">

                        <i class="bi bi-pc-display"></i>

                        <span>Inventar</span>

                    </div>

                    <a href="<?= url('modules/assets/assets/index.php') ?>"
                        class="btn btn-sm btn-primary">
                        Otvori
                    </a>

                </div>

                <div class="dashboard-card-description">

                    Pregled IT opreme

                </div>

            </div>

        </div>

        <div class="col-md-6 col-xl-3">

            <div class="dashboard-card">

                <div class="dashboard-card-header">

                    <div class="dashboard-card-title">

                        <i class="bi bi-person-workspace"></i>

                        <span>Zaduženja</span>

                    </div>

                    <a href="<?= url('modules/assets/assignments/index.php') ?>"
                        class="btn btn-sm btn-primary">
                        Otvori
                    </a>

                </div>

                <div class="dashboard-card-description">

                    Pregled zaduženja opreme

                </div>

            </div>

        </div>

    </div>

    <!-- ADMINISTRACIJA -->

    <h5 class="mb-3 text-uppercase text-muted">
        Administracija
    </h5>

    <div class="row g-3">

        <div class="col-md-6 col-xl-3">

            <div class="dashboard-card">

                <div class="dashboard-card-header">

                    <div class="dashboard-card-title">

                        <i class="bi bi-person-badge"></i>

                        <span>Korisnici</span>

                    </div>

                    <a href="<?= url('admin/users.php') ?>"
                        class="btn btn-sm btn-primary">
                        Otvori
                    </a>
                </div>

                <div class="dashboard-card-description">

                    Upravljanje korisničkim nalozima

                </div>

            </div>

        </div>

        <div class="col-md-6 col-xl-3">

            <div class="dashboard-card">

                <div class="dashboard-card-header">

                    <div class="dashboard-card-title">

                        <i class="bi bi-clock-history"></i>

                        <span>Audit log</span>

                    </div>

                    <a href="<?= url('admin/audit_logs.php') ?>"
                        class="btn btn-sm btn-primary">
                        Otvori
                    </a>

                </div>

                <div class="dashboard-card-description">

                    Evidencija aktivnosti sistema

                </div>

            </div>

        </div>

    </div>