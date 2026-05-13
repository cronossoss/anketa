<?php

$pageTitle = "Korisnički nalozi";
include "../layouts/admin_layout_start.php";

require_login();
require_role(['admin', 'it']);

/* =========================
   DATA
========================= */

$roles = [
    'admin' => 'Admin',
    'it' => 'IT',
    'hr' => 'HR',
    'manager' => 'Manager',
    'user' => 'User'
];

$employees = $conn->query("
    SELECT 
        e.id,
        e.first_name,
        e.last_name,
        e.position,
        'user' AS system_role
    FROM employees e
    LEFT JOIN users u ON u.employee_id = e.id
    WHERE e.has_account = 1
    AND u.id IS NULL
    ORDER BY e.first_name
");

$users = $conn->query("
    SELECT 
        u.id,
        u.email,
        u.role,
        e.first_name,
        e.last_name,
        e.position
    FROM users u
    LEFT JOIN employees e ON e.id = u.employee_id
    ORDER BY e.first_name
");

?>

<main class="main-content">

    <div class="page-card">

        <?php if ($employees->num_rows == 0): ?>

            <div class="alert alert-info">
                Svi rukovodioci već imaju korisničke naloge.
            </div>

        <?php endif; ?>

        <h3>Korisnici</h3>

        <div class="card p-3 mb-3 shadow-sm">

            <form
                method="POST"
                action="<?= BASE_URL ?>admin/actions/users_create.php" class="row g-2">
                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

                <div class="col-md-9">
                    <select class="form-control" name="employee_id" required>
                        <option value="">-- Izaberi zaposlenog --</option>

                        <?php while ($e = $employees->fetch_assoc()): ?>
                            <option value="<?= $e['id'] ?>">

                                <?= e($e['first_name'] . ' ' . $e['last_name']) ?>

                                (<?= e($e['position'] ?? '-') ?>)

                                

                            </option>
                        <?php endwhile; ?>

                    </select>
                </div>

                <div class="col-md-3">
                    <button class="btn btn-primary w-100">Kreiraj korisnika</button>
                </div>

            </form>

        </div>

        <div class="card p-3 shadow-sm">
            <div class="table-wrapper">

                <table class="table table-hover align-middle">

                    <thead>

                        <tr>

                            <th>Username</th>

                            <th class="d-none d-md-table-cell">
                                Zaposleni
                            </th>

                            <th>Pozicija</th>

                            <th class="d-none d-md-table-cell">
                                Rola
                            </th>

                            <th class="text-end d-none d-md-table-cell">
                                Akcije
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php while ($u = $users->fetch_assoc()): ?>
                            <tr>

                                <td>

                                    <div class="fw-semibold">
                                        <?= e($u['email']) ?>
                                    </div>

                                    <div class="small text-muted d-md-none mt-1">
                                        <?= e($u['first_name'] . ' ' . $u['last_name']) ?>
                                    </div>

                                </td>

                                <td class="d-none d-md-table-cell">
                                    <?= e($u['first_name'] . ' ' . $u['last_name']) ?>
                                </td>

                                <td>
                                    <?= e($u['position'] ?? '-') ?>
                                </td>

                                <td class="d-none d-md-table-cell">

                                    <?php

                                    $badgeClass = match ($u['role']) {
                                        'admin' => 'bg-danger',
                                        'it' => 'bg-dark',
                                        'hr' => 'bg-info',
                                        'manager' => 'bg-warning text-dark',
                                        default => 'bg-secondary'
                                    };

                                    ?>

                                    <div class="d-flex align-items-center gap-2">

                                        <select
                                            class="form-select form-select-sm user-role-select"
                                            data-id="<?= $u['id'] ?>"
                                            style="width: 140px;">

                                            <?php foreach ($roles as $value => $label): ?>

                                                <option
                                                    value="<?= $value ?>"
                                                    <?= ($u['role'] === $value) ? 'selected' : '' ?>>

                                                    <?= $label ?>

                                                </option>

                                            <?php endforeach; ?>

                                        </select>

                                        <span class="badge role-badge <?= $badgeClass ?>">
                                            <?= e($roles[$u['role']] ?? 'User') ?>
                                        </span>

                                    </div>

                                </td>

                                <td class="text-end d-none d-md-table-cell">

                                    <form
                                        method="POST"
                                        action="<?= BASE_URL ?>admin/actions/users_reset_password.php"
                                        class="d-inline">

                                        <input type="hidden" name="reset_id" value="<?= $u['id'] ?>">
                                        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

                                        <button class="btn btn-sm btn-warning">
                                            Reset lozinke
                                        </button>

                                    </form>

                                    <form
                                        method="POST"
                                        action="<?= BASE_URL ?>admin/actions/users_delete.php"
                                        class="d-inline"
                                        onsubmit="return confirm('Obrisati korisnika?');">

                                        <input type="hidden" name="delete_id" value="<?= $u['id'] ?>">
                                        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

                                        <button class="btn btn-sm btn-danger">
                                            Obriši
                                        </button>

                                    </form>

                                </td>

                            </tr>

                            <tr class="d-md-none bg-light">

                                <td colspan="3">

                                    <div class="mb-2">

                                        <div class="small text-muted mb-1">
                                            Rola
                                        </div>

                                        <div class="d-flex align-items-center gap-2">

                                            <select
                                                class="form-select form-select-sm user-role-select"
                                                data-id="<?= $u['id'] ?>">

                                                <?php foreach ($roles as $value => $label): ?>

                                                    <option
                                                        value="<?= $value ?>"
                                                        <?= ($u['role'] === $value) ? 'selected' : '' ?>>

                                                        <?= $label ?>

                                                    </option>

                                                <?php endforeach; ?>

                                            </select>

                                            <span class="badge role-badge <?= $badgeClass ?>">
                                                <?= e($roles[$u['role']] ?? 'User') ?>
                                            </span>

                                        </div>

                                    </div>

                                    <div class="d-grid gap-2">

                                        <form
                                            method="POST"
                                            action="<?= BASE_URL ?>admin/actions/users_reset_password.php">

                                            <input type="hidden" name="reset_id" value="<?= $u['id'] ?>">
                                            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

                                            <button class="btn btn-sm btn-warning w-100">
                                                Reset lozinke
                                            </button>

                                        </form>

                                        <form
                                            method="POST"
                                            action="<?= BASE_URL ?>admin/actions/users_delete.php"
                                            onsubmit="return confirm('Obrisati korisnika?');">

                                            <input type="hidden" name="delete_id" value="<?= $u['id'] ?>">
                                            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

                                            <button class="btn btn-sm btn-danger w-100">
                                                Obriši
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>
                        <?php endwhile; ?>

                    </tbody>
                </table>
            </div>

        </div>
    </div>

</main>

<?php include "../layouts/footer.php"; ?>

