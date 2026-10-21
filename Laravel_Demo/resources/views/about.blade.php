<x-app-shell
    title="About"
    eyebrow="About the project"
    heading="A practical Laravel project built for learning."
    description="This task manager brings together routes, controllers, Blade views, Eloquent models, and MySQL in one simple application."
>
    <div class="grid gap-6 md:grid-cols-[minmax(0,1.25fr)_minmax(280px,0.75fr)]">
        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-200/50 sm:p-8">
            <div class="flex size-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 ring-1 ring-inset ring-blue-100">
                <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.627 48.627 0 0 1 12 20.904a48.627 48.627 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.782.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.5 8.307a24.01 24.01 0 0 0 3.75 1.131" />
                </svg>
            </div>
            <p class="mt-6 text-xs font-semibold uppercase tracking-[0.16em] text-blue-600">Course</p>
            <h2 class="mt-2 text-2xl font-bold tracking-tight text-slate-950">{{ $course }}</h2>
            <p class="mt-2 text-base font-medium text-slate-600">{{ $module }}</p>
            <p class="mt-5 text-sm leading-7 text-slate-600">
                The project demonstrates the MVC pattern through a focused task workflow: users submit a form, Laravel validates the request, Eloquent stores the record, and Blade displays the latest data from MySQL.
            </p>
            <a class="mt-7 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-200" href="{{ route('tasks.index') }}">
                Open Task Manager
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </a>
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-7">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-blue-600">How it works</p>
            <h2 class="mt-2 text-xl font-bold tracking-tight text-slate-950">From request to page</h2>

            <ol class="mt-6 grid gap-5">
                <li class="flex gap-4">
                    <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white">1</span>
                    <div>
                        <h3 class="font-semibold text-slate-900">Route</h3>
                        <p class="mt-1 text-sm leading-6 text-slate-500">Matches the browser URL to the correct controller action.</p>
                    </div>
                </li>
                <li class="flex gap-4">
                    <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-blue-700">2</span>
                    <div>
                        <h3 class="font-semibold text-slate-900">Controller</h3>
                        <p class="mt-1 text-sm leading-6 text-slate-500">Handles input and prepares data for the requested page.</p>
                    </div>
                </li>
                <li class="flex gap-4">
                    <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-slate-100 text-sm font-bold text-slate-700">3</span>
                    <div>
                        <h3 class="font-semibold text-slate-900">Blade view</h3>
                        <p class="mt-1 text-sm leading-6 text-slate-500">Presents the final content in a clean, reusable interface.</p>
                    </div>
                </li>
            </ol>
        </section>
    </div>
</x-app-shell>
