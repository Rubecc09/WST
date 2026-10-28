<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Members') · Netpass</title>
    @fonts
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar" id="app-sidebar">
            <a class="brand" href="{{ route('members.index') }}">
                <span class="brand-mark"><x-icon name="wifi" /></span>
                <span class="brand-copy"><strong>netpass</strong><small>INTERNET CAFÉ</small></span>
            </a>

            <div class="sidebar-label">WORKSPACE</div>
            <nav class="sidebar-nav" aria-label="Main navigation">
                <a class="sidebar-link is-active" href="{{ route('members.index') }}" aria-current="page">
                    <x-icon name="users" />
                    <span>Members</span>
                    <span class="nav-indicator"></span>
                </a>
            </nav>

            <div class="sidebar-bottom">
                <div class="sidebar-help">
                    <span class="help-icon"><x-icon name="sparkles" /></span>
                    <strong>Membership made simple</strong>
                    <p>Keep your community connected and up to date.</p>
                </div>
                <div class="sidebar-user">
                    <span class="user-avatar">NP</span>
                    <span class="user-copy"><strong>Workspace admin</strong><small>Member management</small></span>
                    <x-icon name="more" class="user-more" />
                </div>
            </div>
        </aside>

        <div class="main-column">
            <header class="topbar">
                <button class="mobile-menu" type="button" aria-label="Open navigation" aria-controls="app-sidebar" aria-expanded="false" data-sidebar-toggle>
                    <x-icon name="menu" />
                </button>
                <div class="breadcrumb"><span>Workspace</span><x-icon name="chevron-right" /><strong>@yield('breadcrumb', 'Members')</strong></div>
                <div class="topbar-right">
                    <span class="workspace-pill"><span class="online-dot"></span> Workspace active</span>
                    <span class="topbar-avatar">A</span>
                </div>
            </header>

            <main class="page-content">
                @if (session('success'))
                    <div class="flash-message" role="status">
                        <span class="flash-check"><x-icon name="check" /></span>
                        <span>{{ session('success') }}</span>
                        <button type="button" aria-label="Dismiss notification" data-dismiss-flash><x-icon name="close" /></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <dialog class="confirm-dialog" data-confirm-dialog aria-labelledby="confirm-title">
        <div class="dialog-icon"><x-icon name="pause" /></div>
        <h2 id="confirm-title" data-confirm-title>Deactivate this member?</h2>
        <p data-confirm-message>This member will no longer be active. Their profile and history will be kept.</p>
        <div class="dialog-actions">
            <button type="button" class="button button-secondary" data-confirm-cancel>Cancel</button>
            <button type="button" class="button button-danger" data-confirm-accept>Deactivate member</button>
        </div>
        <p class="dialog-note">This is not permanent deletion. The member can be reactivated at any time.</p>
    </dialog>
</body>
</html>
