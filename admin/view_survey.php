<?php
$pageTitle = "Pregled ankete";

include "../layouts/admin_layout_start.php";

require_login();

require_role(['admin', 'it', 'hr', 'manager']);

$id = $_GET['id'];

// uzmi odgovor (JSON)
$stmt = $conn->prepare("
    SELECT a.answer, e.first_name, e.last_name, o.name as org
    FROM answers a
    JOIN surveys s ON s.id = a.survey_id
    JOIN users u ON u.id = s.user_id
    JOIN employees e ON e.id = u.employee_id
    JOIN organizational_units o ON o.id = e.organizational_unit_id
    WHERE a.survey_id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();

$data = $stmt->get_result()->fetch_assoc();

$answers = json_decode($data['answer'], true);

include "../layout/header.php";
include "../layout/sidebar.php";
?>

<main class="col-lg-10 main-content ms-auto">

<h3><?= e($data['first_name'] . ' ' . $data['last_name']) ?> (<?= e($data['org']) ?>)</h3>

<div class="card p-3 mb-3">
    <b>Desktop računari:</b> <?= e($answers['desktop']) ?><br>
    <b>Laptop računari:</b> <?= e($answers['laptop']) ?>
</div>

<div class="card p-3 mb-3">
    <b>Operativni sistemi:</b><br>
    <?php foreach ($answers['os'] as $k => $v): ?>
        <?= e($k) ?>: <?= e($v) ?><br>
    <?php endforeach; ?>
</div>

<div class="card p-3 mb-3">
    <b>Aplikacije:</b><br>
    <?= implode(", ", $answers['apps']) ?>
</div>

<div class="card p-3 mb-3">
    <b>Potrebni računari:</b> <?= e($answers['need_pc']) ?><br>
    <b>Količina:</b> <?= e($answers['needed_pc_count']) ?>
</div>

<div class="card p-3 mb-3">
    <b>Potrebne aplikacije:</b><br>
    <?= implode(", ", array_filter($answers['needed_apps'])) ?>
</div>

<div class="card p-3 mb-3">
    <b>Obuka:</b> <?= e($answers['training']) ?><br>
    <b>Broj radnika:</b> <?= e($answers['training_count']) ?>
</div>

<div class="card p-3 mb-3">
    <b>Napomena:</b><br>
    <?= e($answers['note']) ?>
</div>

</main>

<?php include "../layout/footer.php"; ?>