<footer class="footer py-3 text-muted">

    <div class="container">

        <!-- DESKTOP -->

        <div class="d-none d-md-flex justify-content-between align-items-center">

            <img
                src="<?= BASE_URL ?>assets/images/logo.png"
                alt="Logo"
                style="height: 64px;">

            <div class="text-end">

                <div class="fw-semibold">
                    Poslovni sistem HK "Krušik"   © <?= date('Y') ?>
                </div>


            </div>

        </div>

        <!-- MOBILE -->

        <div class="d-flex d-md-none flex-column align-items-center text-center gap-2">

            <img
                src="<?= BASE_URL ?>assets/images/logo.png"
                alt="Logo"
                style="height: 50px;">

            <div class="fw-semibold">
                Poslovni sistem HK "Krušik"
            </div>

            <small>
                © <?= date('Y') ?>
            </small>

        </div>

    </div>

</footer>

<script>
    window.APP = {

        csrfToken: '<?= csrf_token() ?>',

        baseUrl: '<?= BASE_URL ?>'
    };
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script src="https://npmcdn.com/flatpickr/dist/l10n/sr.js"></script>

<script src="<?= BASE_URL ?>assets/js/modal.js"></script>

<script src="<?= BASE_URL ?>assets/js/ajax.js"></script>

<script src="<?= BASE_URL ?>assets/js/app.js"></script>

<script src="<?= BASE_URL ?>assets/js/modules/employees.js"></script>

<script src="<?= BASE_URL ?>assets/js/modules/users.js"></script>

</body>

</html>