@props([
    'title',
    'eyebrow',
    'heading',
    'description',
])

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $description }}">
    <title>{{ $title }} | My Task Manager</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body {{ $attributes->class(['min-h-screen bg-slate-50 font-sans text-slate-900 antialiased']) }}>
    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden" aria-hidden="true">
        <div class="absolute -left-24 -top-24 size-72 rounded-full bg-blue-100/70 blur-3xl"></div>
        <div class="absolute -right-24 top-1/3 size-80 rounded-full bg-sky-100/70 blur-3xl"></div>
    </div>

    <header class="border-b border-slate-200/80 bg-white/90 backdrop-blur">
        <div class="mx-auto flex max-w-5xl flex-col gap-4 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-8">
            <a class="flex w-fit items-center gap-3 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-100" href="{{ route('tasks.index') }}">
                <span class="flex size-11 items-center justify-center rounded-2xl bg-blue-600 text-white shadow-lg shadow-blue-600/20">
                    <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 11.25 11.25 13.5 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </span>
                <span>
                    <span class="block text-xs font-semibold uppercase tracking-[0.18em] text-blue-600">Laravel Project</span>
                    <span class="block text-xl font-bold tracking-tight text-slate-950 sm:text-2xl">My Task Manager</span>
                </span>
            </a>

            <nav class="flex items-center gap-1 rounded-xl bg-slate-100 p-1" aria-label="Main navigation">
                <a class="rounded-lg px-4 py-2 text-sm font-semibold transition {{ request()->is('tasks*') ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:bg-white/70 hover:text-slate-900' }}" href="{{ route('tasks.index') }}">Tasks</a>
                <a class="rounded-lg px-4 py-2 text-sm font-semibold transition {{ request()->is('about') ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:bg-white/70 hover:text-slate-900' }}" href="{{ url('/about') }}">About</a>
            </nav>
        </div>
    </header>

    <main class="mx-auto w-full max-w-5xl px-5 py-10 sm:px-8 sm:py-14">
        <section class="mb-8">
            <span class="inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                <span class="size-1.5 rounded-full bg-blue-500"></span>
                {{ $eyebrow }}
            </span>
            <h1 class="mt-4 max-w-3xl text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl">{{ $heading }}</h1>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600 sm:text-base">{{ $description }}</p>
        </section>

        {{ $slot }}
    </main>

    <footer class="border-t border-slate-200/80 bg-white/70">
        <div class="mx-auto max-w-5xl px-5 py-6 text-center text-xs text-slate-500 sm:px-8">
            Built with Laravel &middot; Simple, focused, and organized
        </div>
    </footer>
</body>
</html>
