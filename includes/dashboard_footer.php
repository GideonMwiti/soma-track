        </div>
    </main>
</div>

<script>const SITE_URL = "<?= SITE_URL ?>";</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= SITE_URL ?>/assets/js/api.js"></script>
<script src="<?= SITE_URL ?>/assets/js/app.js"></script>
<?php if (isset($extraJS)) echo $extraJS; ?>
</body>
</html>
