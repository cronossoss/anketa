<?php
require_once "../config/db.php";

require_once "../helpers/auth.php";
require_once "../helpers/csrf.php";
require_once "../helpers/helpers.php";

require_login();
require_admin();

include "../layout/header.php";
include "../layout/sidebar.php";


// DELETE
if (isset($_POST['delete_id'])) {

    if (!verify_csrf($_POST['csrf'])) die("CSRF");

    $unit_id = $_POST['delete_id'];

    $check = $conn->prepare("
        SELECT id
        FROM employees
        WHERE organizational_unit_id=?
    ");

    $check->bind_param("i", $unit_id);
    $check->execute();

    if ($check->get_result()->num_rows > 0) {

        header("Location: org_units.php?error=has_employees");
        exit;
    }

    $stmt = $conn->prepare("
        DELETE FROM organizational_units
        WHERE id=?
    ");

    $stmt->bind_param("i", $unit_id);
    $stmt->execute();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}


// UPDATE
if (isset($_POST['edit_id'])) {

    if (!verify_csrf($_POST['csrf'])) die("CSRF");

    $stmt = $conn->prepare("
        UPDATE organizational_units
        SET code=?, name=?
        WHERE id=?
    ");

    $stmt->bind_param(
        "ssi",
        $_POST['code'],
        $_POST['name'],
        $_POST['edit_id']
    );

    $stmt->execute();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}


// INSERT
if (isset($_POST['code']) && isset($_POST['name'])) {

    if (!verify_csrf($_POST['csrf'])) die("CSRF");

    $stmt = $conn->prepare("
        INSERT INTO organizational_units (code, name)
        VALUES (?, ?)
    ");

    $stmt->bind_param(
        "ss",
        $_POST['code'],
        $_POST['name']
    );

    $stmt->execute();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}


$units = $conn->query("
    SELECT *
    FROM organizational_units
    ORDER BY name
");
?>



<main class="col-lg-10 main-content ms-auto">

    <div class="page-card">

        <div class="d-flex justify-content-between align-items-center mb-3 mobile-stack">

            <h3 class="m-0">
                Organizacione jedinice
            </h3>

        </div>

        <?php if (isset($_GET['error']) && $_GET['error'] === 'has_employees'): ?>

            <div class="alert alert-danger">
                OJ ima zaposlene i ne može se obrisati.
            </div>

        <?php endif; ?>


        <form method="POST" class="row g-2 mb-4 mobile-stack">

            <input
                type="hidden"
                name="csrf"
                value="<?= csrf_token() ?>">

            <div class="col-md-3">

                <input
                    class="form-control"
                    name="code"
                    placeholder="Šifra (npr. 960)"
                    required>

            </div>

            <div class="col-md-6">

                <input
                    class="form-control"
                    name="name"
                    placeholder="Naziv"
                    required>

            </div>

            <div class="col-md-3">

                <button class="btn btn-success w-100">
                    Dodaj
                </button>

            </div>

        </form>



        <div class="table-wrapper">

            <table class="table table-hover align-middle">

                <thead>

                    <tr>
                        <th>Šifra</th>
                        <th>Naziv</th>
                        <th class="text-end">Akcije</th>
                    </tr>

                </thead>

                <tbody>

                    <?php while ($u = $units->fetch_assoc()): ?>

                        <tr>

                            <td><?= e($u['code']) ?></td>
                            <td><?= e($u['name']) ?></td>

                            <td class="text-end">

                                <button
                                    class="btn btn-sm btn-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal"
                                    data-id="<?= $u['id'] ?>"
                                    data-code="<?= e($u['code']) ?>"
                                    data-name="<?= e($u['name']) ?>">

                                    Edit
                                </button>


                                <form
                                    method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('Obrisati organizacionu jedinicu?');">

                                    <input
                                        type="hidden"
                                        name="delete_id"
                                        value="<?= $u['id'] ?>">

                                    <input
                                        type="hidden"
                                        name="csrf"
                                        value="<?= csrf_token() ?>">

                                    <button class="btn btn-sm btn-danger">
                                        Delete
                                    </button>

                                </form>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    </div>

</main>



<!-- EDIT MODAL -->
<div class="modal fade" id="editModal" tabindex="-1">

    <div class="modal-dialog">

        <div class="modal-content">

            <form method="POST">

                <div class="modal-header">

                    <h5 class="modal-title">
                        Izmena organizacione jedinice
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    <input
                        type="hidden"
                        name="edit_id"
                        id="edit_id">

                    <input
                        type="hidden"
                        name="csrf"
                        value="<?= csrf_token() ?>">


                    <input
                        class="form-control mb-2"
                        name="code"
                        id="edit_code"
                        required>

                    <input
                        class="form-control"
                        name="name"
                        id="edit_name"
                        required>

                </div>

                <div class="modal-footer">

                    <button class="btn btn-primary">
                        Sačuvaj
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>



<script>

document.addEventListener('DOMContentLoaded', function() {

    const modal = document.getElementById('editModal');

    modal.addEventListener('show.bs.modal', function(event) {

        const button = event.relatedTarget;

        if (!button) return;

        document.getElementById('edit_id').value = button.dataset.id;
        document.getElementById('edit_code').value = button.dataset.code;
        document.getElementById('edit_name').value = button.dataset.name;

    });

});

</script>

<?php include "../layout/footer.php"; ?>