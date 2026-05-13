<?php
$pageTitle = "Pregled izveštaja";

include "../layouts/admin_layout_start.php";

require_login();

require_role(['admin', 'it', 'hr', 'manager']);


?>

<main class="col-lg-10 main-content ms-auto">

<h3>Pregled izveštaja</h3>

<table class="table table-bordered">
    <tr>
        <th>Organizaciona jedinica</th>
        <th>Korisnik</th>
        <th>Status</th>
        <th>Akcija</th>
    </tr>



</table>

</main>

<?php include "../layouts/footer.php"; ?>