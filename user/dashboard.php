<?php
include "../layouts/user_layout_start.php";
?>

<div class="row g-4">

    <div class="col-12 col-sm-6 col-xl-3">

        <div class="card dashboard-card h-100">

            <div class="card-body">

                <h5>
                    Godišnji odmor
                </h5>

                <p class="text-muted mb-0">
                    Podnesi zahtev za godišnji odmor.
                </p>

            </div>

        </div>

    </div>

    <div class="col-12 col-sm-6 col-xl-3">

        <div class="card dashboard-card h-100">

            <div class="card-body">

                <h5>
                    Slobodan dan
                </h5>

                <p class="text-muted mb-0">
                    Zahtev za slobodan dan.
                </p>

            </div>

        </div>

    </div>

    <div class="col-12 col-sm-6 col-xl-3">

        <div class="card dashboard-card h-100">

            <div class="card-body">

                <h5>
                    Izlazak ranije
                </h5>

                <p class="text-muted mb-0">
                    Evidencija izlaska pre kraja smene.
                </p>

            </div>

        </div>

    </div>

    <div class="col-12 col-sm-6 col-xl-3">

        <div class="card dashboard-card h-100">

            <div class="card-body">

                <h5>
                    Dokumenta
                </h5>

                <p class="text-muted mb-0">
                    Predaja i pregled dokumentacije.
                </p>

            </div>

        </div>

    </div>

</div>

<div class="row mt-4">

    <div class="col-lg-8">

        <div class="card dashboard-card">

            <div class="card-body">

                <h5 class="mb-3">
                    Obaveštenja
                </h5>

                <div class="alert alert-primary mb-2">
                    Nema novih obaveštenja.
                </div>

            </div>

        </div>

    </div>

    <div class="col-lg-4">

        <div class="card dashboard-card">

            <div class="card-body">

                <h5 class="mb-3">
                    Status zahteva
                </h5>

                <p class="text-muted">
                    Trenutno nema aktivnih zahteva.
                </p>

            </div>

        </div>

    </div>

</div>

<?php include __DIR__ . "/../layouts/footer.php"; ?>