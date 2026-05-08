<?php
require_once "../config/db.php";

require_login();
require_admin();

include "../layout/header.php";
include "../layout/sidebar.php";


/* =========================
   DATA
========================= */

$result = $conn->query("
    SELECT *
    FROM organizational_units
    ORDER BY code
");

$units = $result->fetch_all(MYSQLI_ASSOC);


/* =========================
   INSERT
========================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!verify_csrf($_POST['csrf'])) {
        die("CSRF");
    }

    $parent_id = ($_POST['type'] === 'OC')
        ? null
        : (int)$_POST['parent_id'];

    $type        = trim($_POST['type']);
    $code        = trim($_POST['code']);
    $name        = trim($_POST['name']);
    $description = trim($_POST['description']);

    $stmt = $conn->prepare("
        INSERT INTO organizational_units
        (
            parent_id,
            type,
            code,
            name,
            description
        )
        VALUES
        (?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "issss",
        $parent_id,
        $type,
        $code,
        $name,
        $description
    );

    $stmt->execute();

    header("Location: organization.php");
    exit;
}


/* =========================
   TREE
========================= */

function renderTree($elements, $parent = null)
{
    $hasChildren = false;

    foreach ($elements as $element) {

        $elementParent = $element['parent_id'];

        if ($parent === null) {

            if ($elementParent !== null) {
                continue;
            }

        } else {

            if ((int)$elementParent !== (int)$parent) {
                continue;
            }
        }

        if (!$hasChildren) {
            $hasChildren = true;
            echo '<ul>';
        }

        echo '<li>';

        $badge = match($element['type']) {
            'OC' => 'primary',
            'OJ' => 'success',
            default => 'secondary'
        };

        echo '
            <div class="tree-item">

                <span class="badge bg-' . $badge . '">
                    ' . e($element['type']) . '
                </span>

                <strong>
                    ' . e($element['code']) . '
                </strong>

                -

                ' . e($element['name']) . '

            </div>
        ';

        renderTree($elements, $element['id']);

        echo '</li>';
    }

    if ($hasChildren) {
        echo '</ul>';
    }
}
?>


<main class="col-lg-10 main-content ms-auto">

    <div class="d-flex align-items-center mb-4">

        <h2 class="mb-0">
            Organizacija firme
        </h2>

    </div>


    <div class="row">

        <!-- TREE -->

        <div class="col-lg-7 mb-4">

            <div class="page-card">

                <h4 class="mb-4">
                    Pregled organizacije
                </h4>

                <div class="org-tree">

                    <?php renderTree($units); ?>

                </div>

            </div>

        </div>


        <!-- FORM -->

        <div class="col-lg-5">

            <div class="page-card">

                <h4 class="mb-4">
                    Dodavanje organizacije
                </h4>

                <form method="POST">

                    <input
                        type="hidden"
                        name="csrf"
                        value="<?= csrf_token() ?>">

                    <div class="mb-3">

                        <label class="form-label">
                            Tip
                        </label>

                        <select
                            name="type"
                            id="type"
                            class="form-select"
                            required>

                            <option value="OC">
                                Organizaciona celina
                            </option>

                            <option value="OJ">
                                Organizaciona jedinica
                            </option>

                            <option value="OD">
                                Organizacioni deo
                            </option>

                        </select>

                    </div>


                    <div class="mb-3">

                        <label class="form-label">
                            Nadređena jedinica
                        </label>

                        <select
                            name="parent_id"
                            id="parentSelect"
                            class="form-select">

                            <option value="">
                                -- Nema --
                            </option>

                            <?php foreach($units as $u): ?>

                                <option
                                    value="<?= $u['id'] ?>"
                                    data-type="<?= $u['type'] ?>">

                                    <?= e($u['code']) ?>
                                    -
                                    <?= e($u['name']) ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <div class="mb-3">

                        <label class="form-label">
                            Šifra
                        </label>

                        <input
                            type="text"
                            name="code"
                            class="form-control"
                            required>

                    </div>


                    <div class="mb-3">

                        <label class="form-label">
                            Naziv
                        </label>

                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            required>

                    </div>


                    <div class="mb-3">

                        <label class="form-label">
                            Opis
                        </label>

                        <textarea
                            name="description"
                            class="form-control"
                            rows="4"></textarea>

                    </div>


                    <button class="btn btn-primary w-100">

                        Sačuvaj

                    </button>

                </form>

            </div>

        </div>

    </div>

</main>


<script src="../assets/js/modules/organization.js"></script>

<?php include "../layout/footer.php"; ?>

</body>
</html>