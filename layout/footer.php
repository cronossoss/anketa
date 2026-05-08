</div>
</div>

<footer class="footer text-center py-3 text-muted">
    © <?= date('Y') ?> Anketa sistem
</footer>

<script>

window.APP = {

    csrfToken:
        '<?= csrf_token() ?>',

    baseUrl:
        '<?= BASE_URL ?>'
};

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="<?= BASE_URL ?>assets/js/modal.js"></script>
<script src="<?= BASE_URL ?>assets/js/ajax.js"></script>
<script src="<?= BASE_URL ?>assets/js/app.js"></script>

<script src="<?= BASE_URL ?>assets/js/modules/users.js"></script>

</body>
</html>