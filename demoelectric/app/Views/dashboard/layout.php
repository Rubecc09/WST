<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title) ?> | WER Electric</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --wer-electric:#0b315d; --accent:#f5a623; }
        body { background:#f4f7fb; color:#263442; }
        .admin-nav { background:var(--wer-electric); }
        .navbar-brand { font-weight:800; }
        .stat-card { border:0; border-left:5px solid var(--accent); box-shadow:0 .4rem 1.2rem rgba(11,49,93,.08); }
        .panel { border:0; border-radius:1rem; box-shadow:0 .4rem 1.5rem rgba(11,49,93,.08); }
        .table > :not(caption) > * > * { vertical-align:middle; }
        .pagination { margin:0; gap:.25rem; }
        .pagination li { list-style:none; }
        .pagination a { display:block; padding:.4rem .7rem; border:1px solid #dee2e6; border-radius:.4rem; text-decoration:none; }
        .pagination .active a { background:#0d6efd; color:#fff; }
    </style>
</head>
<body>
<nav class="navbar navbar-dark admin-nav">
    <div class="container-fluid px-lg-4">
        <a class="navbar-brand" href="<?= base_url('dashboard') ?>"><i class="fas fa-bolt text-warning me-2"></i>WER Electric Admin</a>
        <div class="d-flex align-items-center gap-3 text-white">
            <span class="d-none d-md-inline">Hi, <?= esc(session()->get('displayName')) ?></span>
            <a class="btn btn-outline-light btn-sm" href="<?= base_url() ?>">Website</a>
            <form action="<?= base_url('logout') ?>" method="post" class="m-0">
                <?= csrf_field() ?>
                <button class="btn btn-warning btn-sm" type="submit">Logout</button>
            </form>
        </div>
    </div>
</nav>
<main class="container-fluid p-3 p-lg-4">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show"><?= esc(session()->getFlashdata('success')) ?><button class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif ?>
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger"><strong>Please correct the following:</strong><ul class="mb-0"><?php foreach (session()->getFlashdata('errors') as $error): ?><li><?= esc($error) ?></li><?php endforeach ?></ul></div>
    <?php endif ?>
    <?= $this->renderSection('content') ?>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
