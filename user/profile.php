<?php
include "../layouts/user_layout_start.php";
?>

<div class="row g-4">

    <div class="col-lg-6">

        <div class="card dashboard-card h-100">

            <div class="card-body">

                <h5 class="mb-4">
                    Lični podaci
                </h5>

                <ul class="list-group">

                    <li class="list-group-item">
                        Adresa
                    </li>

                    <li class="list-group-item">
                        Privatni telefon
                    </li>

                    <li class="list-group-item">
                        Privatni email
                    </li>

                    <li class="list-group-item">
                        Kontakt osoba
                    </li>

                </ul>

            </div>

        </div>

    </div>

    <div class="col-lg-6">

        <div class="card dashboard-card h-100">

            <div class="card-body">

                <h5 class="mb-4">
                    Radni podaci
                </h5>

                <ul class="list-group">

                    <li class="list-group-item">
                        Datum zaposlenja
                    </li>

                    <li class="list-group-item">
                        Ugovor važi do
                    </li>

                    <li class="list-group-item">
                        Broj dana godišnjeg odmora
                    </li>

                    <li class="list-group-item">
                        Radni sati za mesec
                    </li>

                    <li class="list-group-item">
                        Odsustva
                    </li>

                </ul>

            </div>

        </div>

    </div>

</div>

<div class="row mt-4">

    <div class="col-lg-6">

        <div class="card dashboard-card">

            <div class="card-body">

                <h5 class="mb-4">
                    Promena lozinke
                </h5>

                <button class="btn btn-primary">
                    Promeni lozinku
                </button>

            </div>

        </div>

    </div>

</div>

<?php include __DIR__ . "/../layouts/footer.php"; ?>