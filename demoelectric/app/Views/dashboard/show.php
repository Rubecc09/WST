<?= $this->extend('dashboard/layout') ?>
<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"><div class="d-flex align-items-center gap-3"><a class="btn btn-outline-secondary" href="<?= base_url('dashboard') ?>">← Back</a><div><h1 class="h2 mb-0"><?= esc($account['customer_name']) ?></h1><span class="text-muted"><?= esc($account['account_number']) ?></span></div></div><a class="btn btn-primary" href="<?= base_url('customers/' . $account['id'] . '/edit') ?>"><i class="fas fa-pen me-2"></i>Edit</a></div>
<div class="card panel"><div class="card-body p-4"><div class="row g-4">
<?php foreach ([['Account Number',$account['account_number']],['Meter Number',$account['meter_number']],['Email',$account['email']],['Phone',$account['phone']],['Connection Type',ucfirst($account['connection_type'])],['Status',ucfirst($account['status'])],['Service Address',$account['address']],['Created',$account['created_at']]] as [$label,$value]): ?>
<div class="col-md-6"><div class="text-muted small text-uppercase fw-semibold mb-1"><?= esc($label) ?></div><div class="fs-5"><?= esc($value) ?></div></div>
<?php endforeach ?>
</div></div></div>
<?= $this->endSection() ?>
