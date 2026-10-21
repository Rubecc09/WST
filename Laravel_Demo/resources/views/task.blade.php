<x-app-shell
    :title="'Task '.$id"
    eyebrow="Route parameter demo"
    heading="A dynamic task preview."
    description="This page shows how a value from the URL can travel through a Laravel controller and appear inside a Blade view."
>
    <section class="mx-auto max-w-3xl overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl shadow-slate-200/50">
        <div class="border-b border-slate-100 bg-gradient-to-r from-blue-600 to-sky-500 px-6 py-8 text-white sm:px-9">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-blue-100">Task preview</p>
                    <h2 class="mt-2 text-2xl font-bold tracking-tight sm:text-3xl">{{ $title }}</h2>
                </div>
                <span class="inline-flex w-fit items-center gap-1.5 rounded-full bg-white/15 px-3 py-1.5 text-xs font-semibold text-white ring-1 ring-inset ring-white/25 backdrop-blur">
                    <span class="size-1.5 rounded-full bg-amber-300"></span>
                    Demo task
                </span>
            </div>
        </div>

        <div class="grid gap-6 p-6 sm:grid-cols-[160px_minmax(0,1fr)] sm:p-9">
            <div class="rounded-2xl border border-blue-100 bg-blue-50 p-5 text-center">
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-blue-600">Task ID</p>
                <p class="mt-2 break-all text-4xl font-bold tracking-tight text-blue-700">{{ $id }}</p>
            </div>

            <div>
                <h3 class="text-lg font-bold text-slate-950">How this page works</h3>
                <p class="mt-2 text-sm leading-7 text-slate-600">
                    The value <span class="rounded-md bg-slate-100 px-2 py-1 font-mono text-xs font-semibold text-slate-700">{{ $id }}</span> comes from the <span class="font-mono text-xs font-semibold text-blue-700">/tasks/{id}</span> URL. Try replacing it with another number to see the page update.
                </p>

                <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                    <a class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-200" href="{{ route('tasks.index') }}">
                        View saved tasks
                    </a>
                    <a class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-100" href="{{ url('/about') }}">
                        About the project
                    </a>
                </div>
            </div>
        </div>
    </section>
</x-app-shell>
