<?php

$pageTitle = "Servisi";

$currentPage = 'asset-services';

include "../../../layouts/admin_layout_start.php";

require_login();

require_role(['admin', 'it']);

$search = trim($_GET['search'] ?? '');

$sql = "
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

    WHERE 1=1
";

$params = [];

$types = '';

if ($search !== '') {

    $sql .= "
        AND (
            a.inventory_number LIKE ?
            OR s.title LIKE ?
            OR CONCAT(
                e.first_name,
                ' ',
                e.last_name
            ) LIKE ?
        )
    ";

    $like = "%{$search}%";

    $params[] = $like;
    $params[] = $like;
    $params[] = $like;

    $types .= 'sss';
}

$sql .= "
    ORDER BY s.received_at DESC
";

$stmt = $conn->prepare($sql);

if (!empty($params)) {

    $stmt->bind_param(
        $types,
        ...$params
    );
}

$stmt->execute();

$result = $stmt->get_result();

?>

<main class="main-content">

    <div class="page-card">

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">

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

        <!-- SEARCH -->

        <form
            method="GET"
            class="service-search mb-4">

            <input
                type="text"
                name="search"
                class="form-control"
                placeholder="Pretraga po inventarskom broju, naslovu ili serviseru..."
                value="<?= e($search) ?>">

            <button
                type="submit"
                class="btn btn-primary">

                <i class="fa-solid fa-search"></i>

            </button>

        </form>

        <?php if (!empty($_SESSION['success'])): ?>

            <div class="alert alert-success">

                <?= $_SESSION['success'] ?>

            </div>

            <?php unset($_SESSION['success']); ?>

        <?php endif; ?>

        <!-- DESKTOP -->

        <div class="table-wrapper d-none d-md-block">

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

        <!-- MOBILE -->

        <?php

        $stmt->execute();

        $mobileResult = $stmt->get_result();

        ?>

        <div class="d-block d-md-none">

            <?php while ($row = $mobileResult->fetch_assoc()): ?>

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

                <div class="service-mobile-card">

                    <div class="d-flex justify-content-between align-items-start mb-2">

                        <div>

                            <div class="fw-bold">

                                <?= e($row['inventory_number']) ?>

                            </div>

                            <div class="small text-muted">

                                <?= e(
                                    $row['manufacturer']
                                    . ' '
                                    . $row['model']
                                ) ?>

                            </div>

                        </div>

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

                    </div>

                    <div class="small mb-3">

                        <div>

                            <strong>Tip:</strong>

                            <?= e(
                                ucfirst($row['service_type'])
                            ) ?>

                        </div>

                        <div>

                            <strong>Naslov:</strong>

                            <?= e($row['title']) ?>

                        </div>

                        <div>

                            <strong>Serviser:</strong>

                            <?=
                            $row['first_name']
                                ? e(
                                    $row['first_name']
                                    . ' '
                                    . $row['last_name']
                                )
                                : '-'
                            ?>

                        </div>

                    </div>

                    <div class="d-flex gap-2">

                        <a
                            href="view.php?id=<?= $row['id'] ?>"
                            class="btn btn-sm btn-primary flex-fill">

                            Pregled

                        </a>

                        <?php if (
                            $row['status'] !== 'returned'
                        ): ?>

                            <a
                                href="view.php?id=<?= $row['id'] ?>"
                                class="btn btn-sm btn-success flex-fill">

                                Završeno

                            </a>

                        <?php endif; ?>

                    </div>

                </div>

            <?php endwhile; ?>

        </div>

    </div>

</main>

<style>

.service-search {

    display: flex;

    gap: 10px;
}

.service-search .form-control {

    border-radius: 12px;
}

.service-search .btn {

    border-radius: 12px;

    min-width: 55px;
}

.service-mobile-card {

    background: #fff;

    border: 1px solid #e5e7eb;

    border-radius: 16px;

    padding: 16px;

    margin-bottom: 14px;

    box-shadow: 0 2px 10px rgba(0,0,0,0.04);
}

@media (max-width: 768px) {

    .service-search {

        flex-direction: column;
    }

    .service-search .btn {

        width: 100%;
    }
}

</style>

<?php include "../../../layouts/footer.php"; ?>