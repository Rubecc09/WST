<?= $this->extend('dashboard/layout') ?>
<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div><h1 class="h2 mb-1">Customer Accounts</h1><p class="text-muted mb-0">Manage PowerFlow Electric service accounts.</p></div>
    <a class="btn btn-primary" href="<?= base_url('customers/new') ?>"><i class="fas fa-plus me-2"></i>Add Customer</a>
</div>
<div class="row g-3 mb-4">
    <?php foreach ([['Total',$totalAccounts,'fa-users','primary'],['Active',$activeAccounts,'fa-circle-check','success'],['Inactive',$inactiveAccounts,'fa-circle-pause','secondary'],['Suspended',$suspendedAccounts,'fa-triangle-exclamation','warning']] as [$label,$value,$icon,$color]): ?>
        <div class="col-6 col-xl-3"><div class="card stat-card h-100"><div class="card-body d-flex justify-content-between align-items-center"><div><div class="text-muted"><?= $label ?></div><div class="display-6 fw-bold"><?= $value ?></div></div><i class="fas <?= $icon ?> fs-2 text-<?= $color ?>"></i></div></div></div>
    <?php endforeach ?>
</div>
<div class="card panel mb-4"><div class="card-body">
    <form action="<?= base_url('dashboard') ?>" method="get" class="row g-2">
        <div class="col-lg-5"><input class="form-control" name="search" value="<?= esc($search) ?>" placeholder="Search name, account, email, or phone"></div>
        <div class="col-md-3 col-lg-2"><select class="form-select" name="status"><option value="">All statuses</option><?php foreach (['active','inactive','suspended'] as $option): ?><option value="<?= $option ?>" <?= $status === $option ? 'selected' : '' ?>><?= ucfirst($option) ?></option><?php endforeach ?></select></div>
        <div class="col-md-3 col-lg-2"><select class="form-select" name="type"><option value="">All types</option><?php foreach (['residential','commercial','industrial'] as $option): ?><option value="<?= $option ?>" <?= $type === $option ? 'selected' : '' ?>><?= ucfirst($option) ?></option><?php endforeach ?></select></div>
        <div class="col-md-3 col-lg-auto"><button class="btn btn-dark w-100" type="submit">Filter</button></div>
        <div class="col-md-3 col-lg-auto"><a class="btn btn-outline-secondary w-100" href="<?= base_url('dashboard') ?>">Clear</a></div>
    </form>
</div></div>
<div class="card panel"><div class="card-body p-0"><div class="table-responsive">
    <table class="table table-hover mb-0"><thead class="table-dark"><tr><th>Account</th><th>Customer</th><th>Contact</th><th>Type</th><th>Status</th><th class="text-end">Actions</th></tr></thead><tbody>
    <?php if ($accounts === []): ?><tr><td colspan="6" class="text-center text-muted py-5">No customer accounts found.</td></tr><?php endif ?>
    <?php foreach ($accounts as $account): ?><tr>
        <td class="fw-semibold"><?= esc($account['account_number']) ?></td><td><?= esc($account['customer_name']) ?><div class="small text-muted"><?= esc($account['meter_number']) ?></div></td><td><?= esc($account['email']) ?><div class="small text-muted"><?= esc($account['phone']) ?></div></td><td><span class="badge text-bg-info"><?= esc(ucfirst($account['connection_type'])) ?></span></td><td><span class="badge text-bg-<?= $account['status'] === 'active' ? 'success' : ($account['status'] === 'inactive' ? 'secondary' : 'warning') ?>"><?= esc(ucfirst($account['status'])) ?></span></td>
        <td class="text-end text-nowrap"><a class="btn btn-sm btn-outline-primary" href="<?= base_url('customers/' . $account['id']) ?>" title="View"><i class="fas fa-eye"></i></a> <a class="btn btn-sm btn-outline-dark" href="<?= base_url('customers/' . $account['id'] . '/edit') ?>" title="Edit"><i class="fas fa-pen"></i></a> <form class="d-inline" action="<?= base_url('customers/' . $account['id'] . '/delete') ?>" method="post" onsubmit="return confirm('Delete this customer account?')"><?= csrf_field() ?><button class="btn btn-sm btn-outline-danger" title="Delete"><i class="fas fa-trash"></i></button></form></td>
    </tr><?php endforeach ?>
    </tbody></table>
</div><div class="d-flex justify-content-end p-3"><?= $pager->only(['search', 'status', 'type'])->links() ?></div></div></div>
<?= $this->endSection() ?>
