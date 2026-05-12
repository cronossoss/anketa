<?php

$pageTitle = "Korisnički nalozi";
include "../layouts/admin_layout_start.php";

require_login();
require_admin();

/* =========================
   DATA
========================= */
$employees = $conn->query("
    SELECT 
        e.id,
        e.first_name,
        e.last_name,
        e.position,
        e.system_role
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
        e.position,
        e.system_role
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

                                - <?= strtoupper(e($e['system_role'] ?? 'user')) ?>

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
                            <th>Zaposleni</th>
                            <th>Pozicija</th>
                            <th>Rola</th>
                            <th class="text-end">Akcije</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php while ($u = $users->fetch_assoc()): ?>
                            <tr>

                                <td><?= e($u['email']) ?></td>
                                <td><?= e($u['first_name'] . ' ' . $u['last_name']) ?></td>
                                <td><?= e($u['position'] ?? '-') ?></td>

                                <td>
                                    <label class="form-check form-switch">
                                        <input
                                            class="form-check-input role-toggle"
                                            type="checkbox"
                                            data-id="<?= $u['id'] ?>"
                                            <?= ($u['role'] === 'admin') ? 'checked' : '' ?>>
                                    </label>

                                    <span class="badge role-badge <?= ($u['role'] === 'admin') ? 'bg-danger' : 'bg-secondary' ?>">
                                        <?= ($u['role'] === 'admin') ? 'Admin' : 'User' ?>
                                    </span>
                                </td>

                                <td class="text-end">

                                    <form
                                        method="POST"
                                        action="<?= BASE_URL ?>admin/actions/users_reset_password.php" class="d-inline">
                                        <input type="hidden" name="reset_id" value="<?= $u['id'] ?>">
                                        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                                        <button class="btn btn-sm btn-warning">Reset lozinke</button>
                                    </form>

                                    <form
                                        method="POST"
                                        action="<?= BASE_URL ?>admin/actions/users_delete.php" class="d-inline" onsubmit="return confirm('Obrisati korisnika?');">
                                        <input type="hidden" name="delete_id" value="<?= $u['id'] ?>">
                                        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                                        <button class="btn btn-sm btn-danger">Obriši</button>
                                    </form>

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