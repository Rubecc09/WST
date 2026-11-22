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
                            <input class="form-control @error('password') is-invalid @enderror" type="password" id="password" name="password" autocomplete="current-password" maxlength="4096" required @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
                        </x-form.field>
                        <label class="remember-label"><input type="checkbox" name="remember" value="1" @checked(old('remember'))> Remember me</label>
                        <button class="button button-primary login-submit" type="submit">Sign in</button>
                    </form>
                </section>
                <p class="login-footer">Authorized administrators only. Contact your system administrator if you need access.</p>
            </div>
        </div>
    </main>
</body>
</html>
