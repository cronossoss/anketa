<?php

$pageTitle = "Servisi";

$currentPage = 'asset-services';

include "../../../layouts/admin_layout_start.php";

require_once $_SERVER['DOCUMENT_ROOT']
    . '/anketa/config/init.php';

require_login();

require_role(['admin', 'it']);

$result = $conn->query("
    SELECT
        s.id,

        s.service_type,
        s.status,

        s.title,

        s.received_at,
        s.completed_at,

        a.inventory_number,
        a.manufacturer,
        a.model,

        e.first_name,
        e.last_name

    FROM asset_services s

    LEFT JOIN assets a
        ON a.id = s.asset_id

    LEFT JOIN employees e
        ON e.id = s.technician_id

    ORDER BY s.received_at DESC
");
?>

<main class="main-content">

    <div class="page-card">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <h4 class="mb-0">

                Servisi i intervencije

            </h4>

            <a
                href="create.php"
                class="btn btn-danger">

                <i class="fa-solid fa-screwdriver-wrench me-1"></i>

                Novi servis

            </a>

        </div>

        <?php if (!empty($_SESSION['success'])): ?>

            <div class="alert alert-success">

                <?= $_SESSION['success'] ?>

            </div>

            <?php unset($_SESSION['success']); ?>

        <?php endif; ?>

        <div class="table-wrapper">

            <table class="table table-hover align-middle">

                <thead>

                    <tr>

                        <th>Inventar</th>

                        <th>Tip</th>

                        <th>Naslov</th>

                        <th>Status</th>

                        <th>Serviser</th>

                        <th>Prijem</th>

                        <th width="220">

                            Akcije

                        </th>

                    </tr>

                </thead>

                <tbody>

                    <?php while ($row = $result->fetch_assoc()): ?>

                        <?php

                        $badge = match ($row['status']) {

                            'open' => 'secondary',

                            'diagnostic' => 'warning',

                            'repairing' => 'danger',

                            'waiting_parts' => 'info',

                            'completed' => 'success',

                            'returned' => 'dark',

                            default => 'secondary'
                        };
                        ?>

                        <tr>

                            <td>

                                <div class="fw-semibold">

                                    <?= e($row['inventory_number']) ?>

                                </div>

                                <small class="text-muted">

                                    <?= e(
                                        $row['manufacturer']
                                            . ' '
                                            . $row['model']
                                    ) ?>

                                </small>

                            </td>

                            <td>

                                <?= e(
                                    ucfirst($row['service_type'])
                                ) ?>

                            </td>

                            <td>

                                <?= e($row['title']) ?>

                            </td>

                            <td>

                                <span class="badge bg-<?= $badge ?>">

                                    <?= e(
                                        ucfirst(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $row['status']
                                            )
                                        )
                                    ) ?>

                                </span>

                            </td>

                            <td>

                                <?=
                                $row['first_name']
                                    ? e(
                                        $row['first_name']
                                            . ' '
                                            . $row['last_name']
                                    )
                                    : '-'
                                ?>

                            </td>

                            <td>

                                <?= e($row['received_at']) ?>

                            </td>

                            <td>

                                <div class="d-flex gap-1 flex-wrap">

                                    <a
                                        href="view.php?id=<?= $row['id'] ?>"
                                        class="btn btn-sm btn-primary">

                                        Pregled

                                    </a>

                                    <?php if (
                                        $row['status'] !== 'returned'
                                    ): ?>

                                        <a
                                            href="view.php?id=<?= $row['id'] ?>"
                                            class="btn btn-sm btn-success">

                                            Završeno

                                        </a>

                                    <?php endif; ?>

                                </div>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    </div>

</main>

<?php include "../../../layouts/footer.php"; ?>