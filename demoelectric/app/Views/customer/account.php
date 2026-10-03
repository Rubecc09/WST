<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<section class="section-padding bg-light-custom"><div class="container">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"><div><h1 class="display-6 fw-bold text-primary-custom mb-1">My Customer Account</h1><p class="text-muted mb-0">Welcome, <?= esc($customer['customer_name']) ?>.</p></div><form action="<?= base_url('customer/logout') ?>" method="post"><?= csrf_field() ?><button class="btn btn-outline-danger" type="submit"><i class="fas fa-right-from-bracket me-2"></i>Logout</button></form></div>
    <div class="card border-0 shadow-sm"><div class="card-body p-4"><div class="row g-4">
    <?php foreach ([['Account Number',$customer['account_number']],['Username',$customer['username']],['Meter Number',$customer['meter_number']],['Email',$customer['email']],['Phone',$customer['phone']],['Connection Type',ucfirst($customer['connection_type'])],['Status',ucfirst($customer['status'])],['Service Address',$customer['address']]] as [$label,$value]): ?>
        <div class="col-md-6"><div class="small text-muted text-uppercase fw-semibold mb-1"><?= esc($label) ?></div><div class="fs-5"><?= esc($value) ?></div></div>
    <?php endforeach ?>
    </div></div></div>
</div></section>
<?= $this->endSection() ?>
