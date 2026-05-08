<?php
require_once "config/db.php";

require_login();

if ($_SESSION['role'] === 'admin') {
    header("Location: admin/dashboard.php");
    exit;
}

// da li već postoji anketa
$stmt = $conn->prepare("SELECT * FROM surveys WHERE user_id=?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$survey = $stmt->get_result()->fetch_assoc();

// ako ne postoji → napravi
if (!$survey) {

    // uzmi org unit iz employee
    $stmt = $conn->prepare("
        SELECT e.organizational_unit_id 
        FROM users u 
        JOIN employees e ON e.id=u.employee_id 
        WHERE u.id=?
    ");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $unit = $stmt->get_result()->fetch_assoc();

    $stmt = $conn->prepare("
        INSERT INTO surveys (user_id, org_unit_id) 
        VALUES (?,?)
    ");
    $stmt->bind_param("ii", $_SESSION['user_id'], $unit['organizational_unit_id']);
    $stmt->execute();

    header("Location: survey.php");
    exit;
}

// ako je već popunjena
if ($survey['status'] === 'submitted') {
    $locked = true;
}

// SUBMIT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!verify_csrf($_POST['csrf'])) die("CSRF");

    $data = [
        "desktop" => $_POST['desktop_count'],
        "laptop" => $_POST['laptop_count'],
        "os" => $_POST['os'] ?? [],
        "apps" => $_POST['apps'] ?? [],
        "need_pc" => $_POST['need_pc'],
        "needed_pc_count" => $_POST['needed_pc_count'],
        "needed_apps" => $_POST['needed_apps'] ?? [],
        "training" => $_POST['training'],
        "training_count" => $_POST['training_count'],
        "note" => $_POST['note']
    ];

    $json = json_encode($data);

    $stmt = $conn->prepare("
        INSERT INTO answers (survey_id, question_id, answer)
        VALUES (?, NULL, ?)
    ");
    $stmt->bind_param("is", $survey['id'], $json);
    $stmt->execute();

    $conn->query("UPDATE surveys SET status='submitted' WHERE id=" . $survey['id']);

    function clean_number($v)
    {
        return max(0, (int)$v);
    }

    $desktop = clean_number($_POST['desktop_count']);
    $laptop = clean_number($_POST['laptop_count']);

    $os = $_POST['os'] ?? [];

    $win7 = clean_number($os['win7'] ?? 0);
    $win10 = clean_number($os['win10'] ?? 0);
    $win11 = clean_number($os['win11'] ?? 0);

    $total_pc = $desktop + $laptop;
    $total_os = $win7 + $win10 + $win11;

    // ❌ VALIDACIJA OS vs računari
    if ($total_os > $total_pc) {
        die("Greška: broj operativnih sistema ne može biti veći od ukupnog broja računara.");
    }

    // ❌ VALIDACIJA negativnih (backup)
    if ($desktop < 0 || $laptop < 0 || $win7 < 0 || $win10 < 0 || $win11 < 0) {
        die("Greška: negativni brojevi nisu dozvoljeni.");
    }



    header("Location: survey.php?success=1");
    exit;
}


$questions = $conn->query("SELECT * FROM questions");

include "layout/header.php";
include "layout/sidebar.php";
?>

<h3>Anketa</h3>

<?php if (!empty($_GET['success'])): ?>

    <div class="alert alert-success shadow-sm">
        Hvala što ste popunili anketu.
    </div>

<?php elseif (!empty($locked)): ?>

    <div class="alert alert-warning">
        Anketa je već popunjena.
    </div>

<?php endif; ?>

<?php if (empty($locked)): ?>

    <form method="POST">

        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

        <!-- 1 -->
        <div class="card p-3 mb-3">
            <label>1. Koliko desktop računara imate?</label>
            <input type="number" class="form-control" name="desktop_count" min="0" required>
        </div>

        <!-- 2 -->
        <div class="card p-3 mb-3">
            <label>2. Koliko laptop računara imate?</label>
            <input type="number" class="form-control" name="laptop_count" min="0">
        </div>

        <!-- 3 -->
        <div class="card p-3 mb-3">
            <label>3. Operativni sistemi</label>

            Windows 7 <input type="number" min="0" name="os[win7]" class="form-control mb-2">
            Windows 10 <input type="number" min="0" name="os[win10]" class="form-control mb-2">
            Windows 11 <input type="number" min="0" name="os[win11]" class="form-control mb-2">
        </div>

        <!-- 4 -->
        <div class="card p-3 mb-3">
            <label>4. Aplikacije</label>

            <?php
            $apps = ["Word", "Excel", "LibreOffice Writer", "LibreOffice Calc", "CorelDraw", "Autocad", "Solidworks", "Axiom", "Drugo"];
            foreach ($apps as $a):
            ?>
                <div>
                    <input type="checkbox" name="apps[]" value="<?= $a ?>"> <?= $a ?>
                </div>
            <?php endforeach; ?>

        </div>

        <!-- 5 -->
        <div class="card p-3 mb-3">
            <label>5. Da li vam treba još računara?</label>
            <select name="need_pc" class="form-control" id="need_pc">
                <option>Da</option>
                <option>Ne</option>
            </select>
        </div>

        <!-- 6 -->
        <div class="card p-3 mb-3" id="pc_count_box">
            <label>6. Koliko računara?</label>
            <input type="number" name="needed_pc_count" class="form-control" min="0">
        </div>

        <!-- 7 -->
        <div class="card p-3 mb-3">
            <label>7. Potrebne aplikacije</label>
            <input class="form-control mb-2" name="needed_apps[]">
            <input class="form-control mb-2" name="needed_apps[]">
            <input class="form-control mb-2" name="needed_apps[]">
        </div>

        <!-- 8 -->
        <div class="card p-3 mb-3">
            <label>8. Da li treba obuka?</label>
            <select name="training" class="form-control">
                <option>Da</option>
                <option>Ne</option>
            </select>
        </div>

        <!-- 9 -->
        <div class="card p-3 mb-3">
            <label>9. Koliko radnika?</label>
            <input type="number" name="training_count" class="form-control" min="0">
        </div>

        <!-- 10 -->
        <div class="card p-3 mb-3">
            <label>10. Napomena</label>
            <textarea name="note" class="form-control"></textarea>
        </div>

        <button class="btn btn-success">Pošalji</button>

    </form>

<?php endif; ?>

<script>
    document.getElementById('need_pc').addEventListener('change', function() {
        let box = document.getElementById('pc_count_box');

        if (this.value === 'Da') {
            box.style.display = 'block';
        } else {
            box.style.display = 'none';
        }
    });
</script>

<?php include "layout/footer.php"; ?>