<?php require_once "../config/db.php";

require_login();
require_admin();
?>

<?php include "../layout/header.php"; ?>
<?php include "../layout/sidebar.php"; ?>

<main class="main-content col-lg-10 ms-auto">

<h3 class="mb-4">Dashboard</h3>

<?php if ($_SESSION['role'] === 'admin'): ?>

    <?php

    $result = $conn->query("
    SELECT a.answer 
    FROM answers a
    JOIN surveys s ON s.id = a.survey_id
    WHERE s.status = 'submitted'
");

if (!$result) {
    die($conn->error);
}

    $total_desktop = 0;
    $total_laptop = 0;
    $total_needed = 0;
    $total_training = 0;

        while ($row = $result->fetch_assoc()) {
    
        $data = json_decode($row['answer'], true);
    
        if (!is_array($data)) {
            continue;
        }
    
        $total_desktop += (int)($data['desktop'] ?? 0);
        $total_laptop += (int)($data['laptop'] ?? 0);
        $total_needed += (int)($data['needed_pc_count'] ?? 0);
        $total_training += (int)($data['training_count'] ?? 0);
    }
    ?>

    <div class="row g-3">

        <div class="col-md-3">
            <div class="card p-3 shadow-sm">
                <h6>Desktop računari</h6>
                <h3><?= $total_desktop ?></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3 shadow-sm">
                <h6>Laptop računari</h6>
                <h3><?= $total_laptop ?></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3 shadow-sm">
                <h6>Potrebni računari</h6>
                <h3><?= $total_needed ?></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3 shadow-sm">
                <h6>Obuka (radnici)</h6>
                <h3><?= $total_training ?></h3>
            </div>
        </div>

    </div>

    <?php

    $os_stats = [];

    $result = $conn->query("SELECT answer FROM answers");

    while ($row = $result->fetch_assoc()) {

        $data = json_decode($row['answer'], true);

        if (isset($data['os'])) {
            foreach ($data['os'] as $os => $count) {
                $os_stats[$os] = ($os_stats[$os] ?? 0) + $count;
            }
        }
    }
    ?>

    <div class="card p-3 mt-4">
        <h5>Operativni sistemi</h5>

        <?php foreach ($os_stats as $os => $count): ?>
            <?= $os ?> : <?= $count ?><br>
        <?php endforeach; ?>

    </div>

<?php else: ?>

    <div class="card p-4">
        <h5>Anketa</h5>
        <a href="<?= BASE_URL ?>survey.php" class="btn btn-primary">
            Popuni anketu
        </a>
    </div>

<?php endif; ?>

</main>

<?php include "../layout/footer.php"; ?>