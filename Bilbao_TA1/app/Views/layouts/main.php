<?php
$router = service('router');
$controller = $router->controllerName();
$method = $router->methodName();
$activePage = match (true) {
    str_ends_with($controller, '\\Pages') => $method === 'about' ? 'about' : 'home',
    str_ends_with($controller, '\\Customers') => 'customers',
    str_ends_with($controller, '\\Users') => 'users',
    default => '',
};
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title) ?> | KONE POS</title>
    <style>
        :root { color-scheme: light; --navy:#173b57; --primary:#173b57; --accent:#df8a32; --bg:#f4f6f8; --text:#1f2933; --muted:#64717d; --border:#dbe2e8; }
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { min-height:100vh; margin:0; display:flex; flex-direction:column; background:var(--bg); color:var(--text); font-family:Inter,"Segoe UI",Roboto,Helvetica,Arial,sans-serif; line-height:1.6; }
        header { position:sticky; top:0; z-index:10; background:rgba(23,59,87,.97); color:#fff; box-shadow:0 5px 18px rgba(23,59,87,.14); backdrop-filter:blur(12px); }
        .nav { width:min(1120px,calc(100% - 2rem)); min-height:74px; margin:auto; display:flex; align-items:center; justify-content:space-between; gap:1.5rem; }
        .brand { display:inline-flex; align-items:center; gap:.7rem; color:#fff; font-size:1.25rem; font-weight:800; letter-spacing:-.03em; text-decoration:none; }
        .brand-mark { display:grid; width:2.15rem; height:2.15rem; place-items:center; border-radius:.7rem; background:var(--accent); color:var(--navy); font-size:.95rem; box-shadow:0 0 0 4px rgba(255,255,255,.08); }
        nav { display:flex; flex-wrap:wrap; gap:.3rem; }
        nav a { position:relative; overflow:hidden; color:#d1d5db; padding:.65rem .9rem; border-radius:.65rem; font-size:.94rem; font-weight:600; text-decoration:none; transition:color .22s ease,background .22s ease,transform .22s ease; }
        nav a::after { position:absolute; right:.9rem; bottom:.3rem; left:.9rem; height:2px; border-radius:99px; background:var(--accent); content:""; transform:scaleX(0); transition:transform .25s ease; }
        nav a:hover,nav a:focus-visible { background:rgba(255,255,255,.09); color:#fff; transform:translateY(-2px); }
        nav a:hover::after,nav a:focus-visible::after,nav a.active::after { transform:scaleX(1); }
        nav a.active { background:#fff; color:#000; box-shadow:0 8px 20px rgba(0,0,0,.18); animation:active-nav .35s ease both; }
        @keyframes active-nav { from { opacity:.5; transform:translateY(-5px); } to { opacity:1; transform:translateY(0); } }
        main { width:min(1120px,calc(100% - 2rem)); margin:2.5rem auto; flex:1; animation:page-in .45s ease both; }
        @keyframes page-in { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }
        .hero,.card { background:rgba(255,255,255,.96); border:1px solid rgba(255,255,255,.8); border-radius:1.2rem; box-shadow:0 8px 24px rgba(23,59,87,.09); padding:clamp(1.5rem,4vw,3rem); }
        .hero { overflow:hidden; background:#fff; border-left:.4rem solid var(--accent); }
        .eyebrow { margin:0 0 .5rem; color:var(--primary); font-size:.78rem; font-weight:800; letter-spacing:.13em; text-transform:uppercase; }
        h1,h2,h3 { line-height:1.18; letter-spacing:-.025em; }
        h1 { color:var(--navy); margin:0 0 .8rem; font-size:clamp(2.2rem,7vw,4.2rem); }
        h2 { margin:0 0 .55rem; color:var(--navy); font-size:clamp(1.5rem,3vw,2rem); }
        h3 { margin:0 0 .4rem; color:var(--navy); }
        .lead { max-width:670px; margin:0; color:var(--muted); font-size:1.08rem; }
        .actions { display:flex; flex-wrap:wrap; gap:.75rem; margin-top:1.6rem; }
        .button { display:inline-flex; align-items:center; gap:.5rem; padding:.72rem 1.1rem; border:1px solid var(--primary); border-radius:.65rem; background:var(--primary); color:#fff; font-weight:700; text-decoration:none; box-shadow:0 8px 18px rgba(23,59,87,.18); transition:transform .2s ease,box-shadow .2s ease; }
        .button.secondary { border-color:var(--border); background:#fff; color:var(--navy); box-shadow:none; }
        .button:hover,.button:focus-visible { transform:translateY(-3px); box-shadow:0 12px 24px rgba(23,59,87,.22); }
        .section-heading { margin:2.5rem 0 1rem; }
        .section-heading p { margin:0; color:var(--muted); }
        .stats,.feature-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:1rem; }
        .stat,.feature { padding:1.35rem; border:1px solid var(--border); border-radius:1rem; background:#fff; box-shadow:0 10px 28px rgba(31,41,55,.06); }
        .stat strong { display:block; color:var(--primary); font-size:2rem; line-height:1; }
        .stat span,.feature p { color:var(--muted); }
        .feature-icon { display:grid; width:2.7rem; height:2.7rem; margin-bottom:1rem; place-items:center; border-radius:.8rem; background:#fff3e7; color:var(--primary); font-weight:900; }
        .feature p { margin:0; }
        .table-wrap { overflow-x:auto; border:1px solid var(--border); border-radius:.9rem; }
        table { width:100%; border-collapse:collapse; background:#fff; }
        th,td { padding:.9rem 1rem; border-bottom:1px solid var(--border); text-align:left; }
        th { background:#e9eef2; color:var(--navy); font-size:.8rem; letter-spacing:.04em; text-transform:uppercase; }
        tbody tr { transition:background .18s ease; }
        tbody tr:hover { background:#f8fafb; }
        tbody tr:last-child td { border-bottom:0; }
        footer { margin-top:auto; background:var(--navy); color:#cbd5e1; }
        .footer-inner { width:min(1120px,calc(100% - 2rem)); margin:auto; padding:1.6rem 0; text-align:center; }
        .footer-inner strong { display:block; margin-bottom:.25rem; color:#fff; }
        .footer-inner p { margin:0; font-size:.9rem; }
        @media (max-width:760px) { .nav { align-items:flex-start; flex-direction:column; padding:1rem 0; } nav { width:100%; } nav a { flex:1; padding-inline:.4rem; text-align:center; } .stats,.feature-grid { grid-template-columns:1fr; } th,td { padding:.7rem; } }
        @media (prefers-reduced-motion:reduce) { *,*::before,*::after { animation-duration:.01ms!important; transition-duration:.01ms!important; } }
    </style>
</head>
<body>
<header>
    <div class="nav">
        <a class="brand" href="<?= site_url('/') ?>"><span class="brand-mark">K</span>KONE POS</a>
        <nav aria-label="Main navigation">
            <a class="<?= $activePage === 'home' ? 'active' : '' ?>" href="<?= site_url('/') ?>" <?= $activePage === 'home' ? 'aria-current="page"' : '' ?>>Home</a>
            <a class="<?= $activePage === 'about' ? 'active' : '' ?>" href="<?= site_url('about') ?>" <?= $activePage === 'about' ? 'aria-current="page"' : '' ?>>About</a>
            <a class="<?= $activePage === 'customers' ? 'active' : '' ?>" href="<?= site_url('customers') ?>" <?= $activePage === 'customers' ? 'aria-current="page"' : '' ?>>Customers</a>
            <a class="<?= $activePage === 'users' ? 'active' : '' ?>" href="<?= site_url('users') ?>" <?= $activePage === 'users' ? 'aria-current="page"' : '' ?>>Users</a>
        </nav>
    </div>
</header>
<main><?= $this->renderSection('content') ?></main>
<footer>
    <div class="footer-inner">
        <strong>Made by Rovic F. Bilbao</strong>
        <p>IT0049 - BSIT WMA - TW33</p>
    </div>
</footer>
</body>
</html>