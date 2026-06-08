<?php

$pageTitle = "Audit log";

include "../layouts/layout_start.php";

require_login();

require_role(['admin', 'it']);

$result = $conn->query("
    SELECT
        al.*,
        u.email,
        e.first_name,
        e.last_name
    FROM audit_logs al

    LEFT JOIN users u
        ON u.id = al.user_id

    LEFT JOIN employees e
        ON e.id = u.employee_id

    ORDER BY al.created_at DESC

    LIMIT 300
");
?>



<main class="main-content">

    <div class="page-card">

        <div class="d-flex justify-content-between mb-3">

  

        </div>

        <div class="table-wrapper">

            <table class="table table-hover align-middle">

                <thead>

                    <tr>

                        <th>Vreme</th>

                        <th>Korisnik</th>

                        <th class="d-none d-md-table-cell">
                            Modul
                        </th>

                        <th class="d-none d-md-table-cell">
                            Akcija
                        </th>

                        <th>Opis</th>

                        <th class="d-none d-md-table-cell">
                            IP
                        </th>

                    </tr>

                </thead>

                <tbody>

                    <?php while ($log = $result->fetch_assoc()): ?>
                        <?php

                        $actionClass = match ($log['action']) {

                            'create' => 'bg-success',

                            'update' => 'bg-warning text-dark',

                            'delete' => 'bg-danger',

                            'password_reset' => 'bg-dark',

                            default => 'bg-secondary'
                        };
                        ?>

                        <tr>

                            <td style="white-space: nowrap;">

                                <?= date(
                                    'd.m.Y H:i',
                                    strtotime($log['created_at'])
                                ) ?>

                            </td>

                            <td>

                                <?=
                                e(
                                    trim(
                                        ($log['first_name'] ?? '') .
                                            ' ' .
                                            ($log['last_name'] ?? '')
                                    )
                                )
                                ?>

                                <div class="small text-muted">

                                    <?= e($log['email'] ?? '-') ?>

                                </div>

                                <div class="d-md-none mt-1">

                                    <span class="badge bg-primary">

                                        <?= e($log['module']) ?>

                                    </span>

                                    <span class="badge <?= $actionClass ?>">

                                        <?= e($log['action']) ?>

                                    </span>

                                </div>

                            </td>

                            <td class="d-none d-md-table-cell">

                                <span class="badge bg-primary">

                                    <?= e($log['module']) ?>

                                </span>

                            </td>

                            <td class="d-none d-md-table-cell">

                                <span class="badge <?= $actionClass ?>">

                                    <?= e($log['action']) ?>

                                </span>

                            </td>

                            <td>

                                <div>

                                    <?= e($log['description']) ?>

                                </div>

                                <div class="d-md-none mt-1 small text-muted">

                                    IP:
                                    <code>

                                        <?= e(
                                            $log['ip_address'] === '::1'
                                                ? 'localhost'
                                                : $log['ip_address']
                                        ) ?>

                                    </code>

                                </div>

                            </td>

                            <td class="d-none d-md-table-cell">

                                <code>

                                    <?= e(
                                        $log['ip_address'] === '::1'
                                            ? 'localhost'
                                            : $log['ip_address']
                                    ) ?>

                                </code>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    </div>

</main>

<?php include "../layouts/layout_end.php"; ?>