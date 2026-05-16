            </div>
            </div>
            </div>

            <footer class="footer text-center py-3 text-muted">
                    © <?= date('Y') ?> Anketa sistem
            </footer>

            <script>
                    window.APP = {

                            csrfToken: <?= json_encode(csrf_token()) ?>,

                            baseUrl: <?= json_encode(BASE_URL) ?>
                    };
            </script>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

            <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

            <script src="https://npmcdn.com/flatpickr/dist/l10n/sr.js"></script>

            <script src="<?= url('assets/js/modal.js') ?>"></script>

            <script src="<?= url('assets/js/ajax.js') ?>"></script>

            <script src="<?= url('assets/js/app.js') ?>"></script>

            <script src="<?= url('assets/js/modules/users.js') ?>"></script>

            </body>

            </html>