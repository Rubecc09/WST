<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="hero">
    <h1>Welcome to Rov's POS</h1>
    <p>This first version provides quick access to customer and staff account information.</p>
    <div class="actions">
        <a class="button" href="<?= site_url('customers') ?>">View customers</a>
        <a class="button" href="<?= site_url('users') ?>">View users</a>
    </div>
</section>
<?= $this->endSection() ?>
