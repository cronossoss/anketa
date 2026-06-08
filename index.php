<?php
require_once __DIR__ . '/config/init.php';

if (isset($_SESSION['user_id'])) {

    redirect('modules/dashboard/index.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!verify_csrf($_POST['csrf'] ?? '')) {
        die("CSRF error");
    }

    $email = $_POST['email'];

    $stmt = $conn->prepare("SELECT 
                                u.*,
                                e.first_name,
                                e.last_name,
                                e.photo
                            FROM users u
                            LEFT JOIN employees e ON e.id = u.employee_id
                            WHERE u.email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user) {

        // brute force zaštita
        if (($user['failed_attempts'] ?? 0) >= 5) {
            $error = "Nalog zaključan (više pokušaja)";
        } else {

            if (password_verify($_POST['password'], $user['password'])) {

                session_regenerate_id(true);

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];

                $_SESSION['name'] =
                    trim(
                        ($user['first_name'] ?? '') . ' ' .
                            ($user['last_name'] ?? '')
                    );

                $_SESSION['photo'] =
                    file_exists(
                        "uploads/employees/" . $user['photo']
                    )
                    ? $user['photo']
                    : null;

                $conn->query(
                    "
                        UPDATE users
                        SET failed_attempts=0
                        WHERE id=" . $user['id']
                );

                redirect('modules/dashboard/index.php');

                exit;
            } else {
                $conn->query("UPDATE users SET failed_attempts = failed_attempts+1 WHERE id=" . $user['id']);
                $error = "Pogrešan login";
            }
        }
    } else {
        $error = "Pogrešan login";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link
        rel="icon"
        type="image/png"
        href="<?= url('assets/images/favicon.png') ?>">

    <title>Krušik login</title>

</head>

<body class="bg-light">

    <div class="container vh-100 d-flex justify-content-center align-items-center">

        <div class="card p-4 shadow" style="width:350px">

            <div class="text-center mb-4">

                <img
                    src="<?= url('assets/images/logo.png') ?>"
                    alt="Logo"
                    style="height: 64px;">


                <h2 class="fw-bold login-title">
                    Integrisani informacioni sistem
                    HK "Krušik" a.d. Valjevo
                </h2>

                <p class="text-muted login-subtitle mb-0">
                    Prijavite se na sistem
                </p>

            </div>

            <h4>Prijava</h4>

            <?php if (isset($_GET['logout'])): ?>
                <div class="alert alert-success">Odjavljeni ste</div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

                <input class="form-control mb-2" name="email" required placeholder="Email">
                <input class="form-control mb-2" type="password" name="password" required placeholder="Lozinka">

                <button class="btn btn-primary w-100">Login</button>
            </form>

        </div>
    </div>

</body>

</html>
<?php ob_end_flush(); ?>