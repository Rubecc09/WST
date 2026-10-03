<?= $this->extend('dashboard/layout') ?>
<?= $this->section('content') ?>
<?php $editing = $account !== null; ?>
<div class="d-flex align-items-center gap-3 mb-4"><a class="btn btn-outline-secondary" href="<?= base_url('dashboard') ?>">← Back</a><div><h1 class="h2 mb-0"><?= esc($title) ?></h1></div></div>
<div class="card panel"><div class="card-body p-4">
<form action="<?= $editing ? base_url('customers/' . $account['id'] . '/update') : base_url('customers') ?>" method="post">
<?= csrf_field() ?>
<div class="row g-3">
<?php
$fields = [
    ['account_number','Account Number','text','EC-2024-0026'], ['customer_name','Customer Name','text','Juan Dela Cruz'],
    ['email','Email Address','email','customer@example.com'], ['phone','Phone Number','text','0917-123-4567'],
    ['meter_number','Meter Number','text','MTR-026'],
];
foreach ($fields as [$name,$label,$typeInput,$placeholder]): $value = old($name, $account[$name] ?? ''); ?>
<div class="col-md-6"><label class="form-label" for="<?= $name ?>"><?= $label ?></label><input class="form-control" id="<?= $name ?>" name="<?= $name ?>" type="<?= $typeInput ?>" value="<?= esc($value) ?>" placeholder="<?= esc($placeholder) ?>" required></div>
<?php endforeach ?>
<div class="col-md-6"><label class="form-label" for="connection_type">Connection Type</label><select class="form-select" id="connection_type" name="connection_type" required><?php $selectedType=old('connection_type',$account['connection_type']??'residential'); foreach(['residential','commercial','industrial'] as $option): ?><option value="<?= $option ?>" <?= $selectedType===$option?'selected':'' ?>><?= ucfirst($option) ?></option><?php endforeach ?></select></div>
<div class="col-md-6"><label class="form-label" for="status">Status</label><select class="form-select" id="status" name="status" required><?php $selectedStatus=old('status',$account['status']??'active'); foreach(['active','inactive','suspended'] as $option): ?><option value="<?= $option ?>" <?= $selectedStatus===$option?'selected':'' ?>><?= ucfirst($option) ?></option><?php endforeach ?></select></div>
<div class="col-12"><label class="form-label" for="address">Service Address</label><textarea class="form-control" id="address" name="address" rows="3" required><?= esc(old('address',$account['address']??'')) ?></textarea></div>
</div><div class="mt-4"><button class="btn btn-primary" type="submit"><i class="fas fa-floppy-disk me-2"></i><?= $editing ? 'Save Changes' : 'Create Account' ?></button> <a class="btn btn-light" href="<?= base_url('dashboard') ?>">Cancel</a></div>
</form></div></div>
<?= $this->endSection() ?>
