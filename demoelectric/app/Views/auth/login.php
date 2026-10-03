<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title) ?> | PowerFlow Electric</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { min-height:100vh; display:grid; place-items:center; background:linear-gradient(135deg,#081f3d,#0d6efd); padding:1rem; }
        .login-card { width:min(440px,100%); border:0; border-radius:1.25rem; box-shadow:0 1.5rem 4rem rgba(0,0,0,.3); }
        .brand-icon { display:grid; width:4rem; height:4rem; place-items:center; margin:auto; border-radius:1rem; background:#ffc107; color:#081f3d; font-size:2rem; }
    </style>
</head>
<body>
<div class="card login-card">
    <div class="card-body p-4 p-md-5">
        <div class="text-center mb-4">
            <div class="brand-icon mb-3"><i class="fas fa-bolt"></i></div>
            <h1 class="h3 fw-bold mb-1">PowerFlow Electric</h1>
            <p class="text-muted">Customer Management Login</p>
        </div>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif ?>
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
        <?php endif ?>
        <form action="<?= base_url('login') ?>" method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input id="username" name="username" class="form-control form-control-lg" value="<?= esc(old('username')) ?>" required autofocus>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <input id="password" name="password" type="password" class="form-control form-control-lg" required>
            </div>
            <button class="btn btn-primary btn-lg w-100" type="submit"><i class="fas fa-right-to-bracket me-2"></i>Log In</button>
        </form>
        <a class="d-block text-center mt-4" href="<?= base_url() ?>">← Back to company website</a>
    </div>
</div>
</body>
</html>
