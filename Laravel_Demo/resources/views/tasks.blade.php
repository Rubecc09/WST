<x-app-shell
    title="Tasks"
    eyebrow="Simple productivity"
    heading="Keep your day clear and organized."
    description="Add the work that matters, review your saved tasks, and stay focused on what comes next."
>
        @if (session('success'))
            <div class="mb-6 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3.5 text-sm font-medium text-emerald-800 shadow-sm" role="status">
                <svg class="mt-0.5 size-5 shrink-0 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <div class="grid items-start gap-8 lg:grid-cols-[minmax(0,360px)_minmax(0,1fr)]">
            <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-200/50 sm:p-7">
                <div class="mb-6">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-blue-600">New task</p>
                    <h2 class="mt-2 text-xl font-bold tracking-tight text-slate-950">What needs to be done?</h2>
                    <p class="mt-1.5 text-sm leading-6 text-slate-500">Give your task a clear title and an optional note.</p>
                </div>

                <form class="grid gap-5" action="{{ route('tasks.store') }}" method="POST">
                    @csrf

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700" for="task_title">Task title</label>
                        <input
                            class="w-full rounded-xl border bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:bg-white focus:ring-4 {{ $errors->has('task_title') ? 'border-red-400 focus:border-red-500 focus:ring-red-100' : 'border-slate-200 hover:border-slate-300 focus:border-blue-500 focus:ring-blue-100' }}"
                            type="text"
                            id="task_title"
                            name="task_title"
                            value="{{ old('task_title') }}"
                            placeholder="e.g. Finish Laravel activity"
                            maxlength="255"
                            required
                        >
                        @error('task_title')
                            <p class="mt-2 flex items-center gap-1.5 text-sm font-medium text-red-600">
                                <span class="size-1.5 rounded-full bg-red-500"></span>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700" for="description">
                            Description <span class="font-normal text-slate-400">(optional)</span>
                        </label>
                        <textarea
                            class="min-h-28 w-full resize-y rounded-xl border bg-slate-50 px-4 py-3 text-sm leading-6 text-slate-900 outline-none transition placeholder:text-slate-400 focus:bg-white focus:ring-4 {{ $errors->has('description') ? 'border-red-400 focus:border-red-500 focus:ring-red-100' : 'border-slate-200 hover:border-slate-300 focus:border-blue-500 focus:ring-blue-100' }}"
                            id="description"
                            name="description"
                            placeholder="Add a helpful note about this task..."
                        >{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-2 flex items-center gap-1.5 text-sm font-medium text-red-600">
                                <span class="size-1.5 rounded-full bg-red-500"></span>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <button class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700 hover:shadow-xl hover:shadow-blue-600/25 focus:outline-none focus:ring-4 focus:ring-blue-200 active:translate-y-0" type="submit">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Add Task
                    </button>
                </form>
            </section>

            <section aria-labelledby="saved-tasks-heading">
                <div class="mb-4 flex items-end justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-blue-600">Your list</p>
                        <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-950" id="saved-tasks-heading">Saved Tasks</h2>
                    </div>
                    <span class="rounded-full border border-slate-200 bg-white px-3 py-1 text-sm font-semibold text-slate-600 shadow-sm">
                        {{ $tasks->count() }} {{ Str::plural('task', $tasks->count()) }}
                    </span>
                </div>

                <div class="grid gap-4">
                    @forelse ($tasks as $task)
                        <article class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-lg hover:shadow-slate-200/60 sm:p-6">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <h3 class="break-words text-lg font-bold tracking-tight text-slate-900 transition group-hover:text-blue-700">{{ $task->task_title }}</h3>
                                    <p class="mt-2 break-words text-sm leading-6 text-slate-600">{{ $task->description ?: 'No description provided.' }}</p>
                                </div>

                                @if ($task->completed)
                                    <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                        <span class="size-1.5 rounded-full bg-emerald-500"></span>
                                        Completed
                                    </span>
                                @else
                                    <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 ring-1 ring-inset ring-amber-200">
                                        <span class="size-1.5 rounded-full bg-amber-500"></span>
                                        Pending
                                    </span>
                                @endif
                            </div>
                        </article>
                    @empty
                        <div class="rounded-3xl border border-dashed border-slate-300 bg-white/70 px-6 py-12 text-center">
                            <span class="mx-auto flex size-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                                <svg class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z" />
                                </svg>
                            </span>
                            <h3 class="mt-4 text-base font-bold text-slate-800">Your task list is ready</h3>
                            <p class="mx-auto mt-1.5 max-w-sm text-sm leading-6 text-slate-500">Add your first task using the form and it will appear here.</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
</x-app-shell>
