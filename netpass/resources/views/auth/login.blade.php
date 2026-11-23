<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Administrator login · NetPass</title>
    @fonts
    <link rel="stylesheet" href="{{ asset('css/bootstrap-grid.min.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .login-password-field { position: relative; width: 100%; }
        .login-password-field .form-control { padding-right: 54px; }
        .login-password-toggle { position: absolute; right: 4px; top: 50%; transform: translateY(-50%); display: grid; place-items: center; width: 44px; height: 40px; padding: 0; border: 0; border-radius: 2px; background: transparent; color: #00f0ff; cursor: pointer; }
        .login-password-toggle:hover { background: #00f0ff18; }
        .login-password-toggle:focus-visible { outline: 2px solid #00f0ff; outline-offset: -2px; }
        .login-password-toggle svg { display: block; width: 22px; height: 22px; grid-area: 1 / 1; pointer-events: none; }
        .login-password-toggle [data-password-eye-off] { display: none; }
        .login-password-toggle[data-visible="true"] [data-password-eye] { display: none; }
        .login-password-toggle[data-visible="true"] [data-password-eye-off] { display: block; }
        .login-password-toggle.is-glowing svg { animation: password-eye-glow 0.5s ease-out; }
        @keyframes password-eye-glow {
            0%, 100% { opacity: 1; filter: drop-shadow(0 0 0 transparent); }
            30%, 70% { opacity: 1; filter: drop-shadow(0 0 5px #00f0ff); }
            50% { opacity: 0.75; filter: drop-shadow(0 0 2px #00f0ff); }
        }
        @media (prefers-reduced-motion: reduce) {
            .login-password-toggle.is-glowing svg { animation: none; }
        }
        .login-password-field input::-ms-reveal { display: none; }
    </style>
</head>
<body class="login-page">
    <main class="login-shell container">
        <div class="row login-terminal-layout">
            <div class="col-12 col-lg-6 login-intro">
                <a class="brand login-brand" href="{{ route('login') }}">
                    <span class="brand-mark"><x-icon name="wifi" /></span>
                    <span class="brand-copy"><strong>netpass</strong><small>INTERNET CAFÉ</small></span>
                </a>
                <p class="login-tagline">Internet Café Membership Management System</p>
                <h2 class="terminal-title">Your café.<br>Your community.<br><span>Your command.</span></h2>
                <p class="terminal-description">One workspace for your members, memberships, and the next chapter of your café.</p>
                <div class="terminal-motif" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i></div>
            </div>
            <div class="col-12 col-lg-6">
                <section class="login-card" aria-labelledby="login-heading">
                    <div class="eyebrow"><span class="eyebrow-line"></span> ADMINISTRATOR ACCESS</div>
                    <h1 id="login-heading">Welcome back</h1>
                    <p class="page-description">Sign in to your administrator account.</p>
                    @if ($errors->any())
                        <div class="validation-summary login-errors" role="alert">{{ $errors->first() }}</div>
                    @endif
                    <form action="{{ route('login.store') }}" method="POST" class="login-form">
                        @csrf
                        <x-form.field name="email" label="Email address" :required="true">
                            <input class="form-control @error('email') is-invalid @enderror" type="email" id="email" name="email" value="{{ old('email') }}" autocomplete="username" maxlength="255" required autofocus @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                        </x-form.field>
                        <x-form.field name="password" label="Password" :required="true">
                            <div class="login-password-field">
                            <input class="form-control @error('password') is-invalid @enderror" type="password" id="password" name="password" autocomplete="current-password" maxlength="4096" required @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
                                <button class="login-password-toggle" id="password-toggle" type="button" aria-label="Show password" aria-controls="password" title="Show password">
                                    <svg data-password-eye width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z" /><circle cx="12" cy="12" r="3" /></svg>
                                    <svg data-password-eye-off width="22" height="22" focusable="false" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8M9.9 5.2A11 11 0 0 1 12 5c6.5 0 10 7 10 7a17 17 0 0 1-3.1 3.8M6.6 6.6A18 18 0 0 0 2 12s3.5 7 10 7a11 11 0 0 0 5.4-1.4" /></svg>
                                </button>
                            </div>
                        </x-form.field>
                        <label class="remember-label"><input type="checkbox" name="remember" value="1" @checked(old('remember'))> Remember me</label>
                        <button class="button button-primary login-submit" type="submit">Sign in</button>
                    </form>
                </section>
                <p class="login-footer">Authorized administrators only. Contact your system administrator if you need access.</p>
            </div>
        </div>
    </main>
    <script>
        (() => {
            const password = document.getElementById('password');
            const toggle = document.getElementById('password-toggle');
            let glowTimeout;
            toggle.addEventListener('click', () => {
                const visible = password.type === 'password';
                password.type = visible ? 'text' : 'password';
                const label = visible ? 'Hide password' : 'Show password';
                toggle.setAttribute('aria-label', label);
                toggle.title = label;
                toggle.dataset.visible = String(visible);
                clearTimeout(glowTimeout);
                toggle.classList.remove('is-glowing');
                if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                    void toggle.offsetWidth;
                    toggle.classList.add('is-glowing');
                    glowTimeout = setTimeout(() => toggle.classList.remove('is-glowing'), 500);
                }
            });
        })();
    </script>
</body>
</html>
