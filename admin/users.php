<?php
require_once "../config/db.php";

require_once "../helpers/auth.php";
require_once "../helpers/csrf.php";
require_once "../helpers/helpers.php";

require_login();
require_admin();

/* =========================
   NORMALIZACIJA USERNAME
========================= */
function normalize_username($string)
{
    $map = [
        'č' => 'c',
        'ć' => 'c',
        'ž' => 'z',
        'š' => 's',
        'đ' => 'dj',
        'Č' => 'c',
        'Ć' => 'c',
        'Ž' => 'z',
        'Š' => 's',
        'Đ' => 'dj'
    ];

    $string = strtr($string, $map);
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9\.]/', '', $string);

    return $string;
}

/* =========================
   DATA
========================= */
$employees = $conn->query("
    SELECT e.id, e.first_name, e.last_name, e.position
    FROM employees e
    LEFT JOIN users u ON u.employee_id = e.id
    WHERE e.is_manager = 1
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
");

/* =========================
   CREATE USER
========================= */
if (isset($_POST['employee_id'])) {

    if (!verify_csrf($_POST['csrf'])) die("CSRF");

    $emp_id = $_POST['employee_id'];

    $stmt = $conn->prepare("SELECT * FROM employees WHERE id=?");
    $stmt->bind_param("i", $emp_id);
    $stmt->execute();
    $emp = $stmt->get_result()->fetch_assoc();

    $check = $conn->prepare("SELECT id FROM users WHERE employee_id=?");
    $check->bind_param("i", $emp_id);
    $check->execute();

    if ($check->get_result()->num_rows > 0) {
        die("User već postoji za ovog zaposlenog.");
    }

    if (!$emp['is_manager']) {
        die("Samo rukovodioci mogu imati korisnički nalog.");
    }

    if (!$emp) die("Zaposleni ne postoji");

    $base = normalize_username($emp['first_name'] . "." . $emp['last_name']);
    $username = $base;

    $i = 1;
    while (true) {
        $check = $conn->prepare("SELECT id FROM users WHERE email=?");
        $check->bind_param("s", $username);
        $check->execute();

        if ($check->get_result()->num_rows === 0) break;

        $username = $base . $i;
        $i++;
    }

    $password = password_hash($emp['personal_id'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("
        INSERT INTO users (email, password, employee_id, role)
        VALUES (?, ?, ?, 'user')
    ");
    $stmt->bind_param("ssi", $username, $password, $emp_id);
    $stmt->execute();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

/* =========================
   RESET PASSWORD
========================= */
if (isset($_POST['reset_id'])) {

    if (!verify_csrf($_POST['csrf'])) die("CSRF");

    $id = $_POST['reset_id'];

    $stmt = $conn->prepare("
        SELECT e.personal_id 
        FROM users u
        JOIN employees e ON e.id = u.employee_id
        WHERE u.id=?
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $emp = $stmt->get_result()->fetch_assoc();

    $newPass = password_hash($emp['personal_id'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
    $stmt->bind_param("si", $newPass, $id);
    $stmt->execute();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

/* =========================
   DELETE USER
========================= */
if (isset($_POST['delete_id'])) {

    if (!verify_csrf($_POST['csrf'])) die("CSRF");

    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $_POST['delete_id']);
    $stmt->execute();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

include "../layout/header.php";
include "../layout/sidebar.php";
?>

<main class="col-lg-10 main-content ms-auto">

<?php if ($employees->num_rows == 0): ?>
    <div class="alert alert-warning">
        Nema definisanih rukovodilaca.
    </div>
<?php endif; ?>

<?php if ($employees->num_rows == 0): ?>
    <div class="alert alert-info">
        Svi rukovodioci već imaju korisničke naloge.
    </div>
<?php endif; ?>

<h3>Korisnici</h3>

<div class="card p-3 mb-3 shadow-sm">

    <form method="POST" class="row g-2">
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

                        <form method="POST" class="d-inline">
                            <input type="hidden" name="reset_id" value="<?= $u['id'] ?>">
                            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                            <button class="btn btn-sm btn-warning">Reset PW</button>
                        </form>

                        <form method="POST" class="d-inline" onsubmit="return confirm('Obrisati korisnika?');">
                            <input type="hidden" name="delete_id" value="<?= $u['id'] ?>">
                            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>

                    </td>

                </tr>
            <?php endwhile; ?>

        </tbody>
    </table>

</div>



</main>

<?php include "../layout/footer.php"; ?>