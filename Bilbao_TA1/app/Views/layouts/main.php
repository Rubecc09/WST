<?php $currentPath = trim(service('uri')->getPath(), '/'); ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title) ?> | Rov's POS</title>
    <style>
        :root { color-scheme: light; --primary: #173b57; --accent: #df8a32; --bg: #f4f6f8; --text: #1f2933; }
        * { box-sizing: border-box; }
        body { margin: 0; background: var(--bg); color: var(--text); font-family: Arial, sans-serif; line-height: 1.5; }
        header { background: var(--primary); color: #fff; }
        .nav { max-width: 1050px; margin: auto; padding: 1rem 1.25rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; }
        .brand { color: #fff; font-size: 1.25rem; font-weight: 700; text-decoration: none; }
        nav { display: flex; flex-wrap: wrap; gap: .35rem; }
        nav a { color: #fff; padding: .45rem .7rem; border-radius: .35rem; text-decoration: none; }
        nav a:hover, nav a:focus { background: rgba(255, 255, 255, .16); }
        nav a.active { background: #fff; color: #000; font-weight: 700; }
        main { width: min(1050px, calc(100% - 2rem)); margin: 2.5rem auto; }
        .hero, .card { background: #fff; border-radius: .75rem; box-shadow: 0 4px 18px rgba(23, 59, 87, .08); padding: clamp(1.4rem, 4vw, 3rem); }
        .hero { border-left: .4rem solid var(--accent); }
        h1 { color: var(--primary); margin-top: 0; }
        .actions { display: flex; flex-wrap: wrap; gap: .75rem; margin-top: 1.5rem; }
        .button { display: inline-block; background: var(--primary); color: #fff; padding: .65rem 1rem; border-radius: .4rem; text-decoration: none; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th, td { padding: .85rem 1rem; border-bottom: 1px solid #dbe2e8; text-align: left; }
        th { background: #e9eef2; color: var(--primary); }
        tbody tr:hover { background: #f8fafb; }
        footer { color: #64717d; text-align: center; padding: 1rem 1rem 2rem; }
        @media (max-width: 650px) { .nav { align-items: flex-start; flex-direction: column; } th, td { padding: .65rem; } }
    </style>
</head>
<body>
<header>
    <div class="nav">
        <a class="brand" href="<?= site_url('/') ?>">Rov's POS</a>
        <nav aria-label="Main navigation">
            <a class="<?= $currentPath === '' ? 'active' : '' ?>" href="<?= site_url('/') ?>" <?= $currentPath === '' ? 'aria-current="page"' : '' ?>>Home</a>
            <a class="<?= $currentPath === 'about' ? 'active' : '' ?>" href="<?= site_url('about') ?>" <?= $currentPath === 'about' ? 'aria-current="page"' : '' ?>>About</a>
            <a class="<?= $currentPath === 'customers' ? 'active' : '' ?>" href="<?= site_url('customers') ?>" <?= $currentPath === 'customers' ? 'aria-current="page"' : '' ?>>Customers</a>
            <a class="<?= $currentPath === 'users' ? 'active' : '' ?>" href="<?= site_url('users') ?>" <?= $currentPath === 'users' ? 'aria-current="page"' : '' ?>>Users</a>
        </nav>
    </div>
</header>
<main>
    <?= $this->renderSection('content') ?>
</main>
<footer>&copy; <?= date('Y') ?> Rov's POS</footer>
</body>
</html>
