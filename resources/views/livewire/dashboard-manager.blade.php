<div class="min-h-screen bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 font-sans">
    <div class=" space-y-12">

        <!-- HEADER SECTION -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">
                    AI Task Manager
                </h1>
                <p class="mt-2 text-zinc-500 dark:text-zinc-400 text-sm">
                    Biarkan AI mengatur produktivitas harian Anda.
                </p>
            </div>
            <!-- Badge Status -->
            <span
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-500/10 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/20">
                <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                </svg>
                AI Connected
            </span>
        </div>

        <!-- INPUT AI SECTION (Style: Hero Card) -->
        <div class="relative group">
            <!-- Gradient Glow Effect -->
            <div
                class="absolute -inset-0.5 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl opacity-20 blur group-hover:opacity-40 transition duration-1000 group-hover:duration-200">
            </div>

            <div
                class="relative bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden">
                <form wire:submit.prevent="processPrompt" class="flex flex-col">
                    <div class="p-6">
                        <label for="prompt" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            Buat tugas baru
                        </label>
                        <textarea wire:model="userPrompt" id="prompt" rows="3"
                            class="block w-full border-0 bg-transparent p-0 text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 focus:ring-0 sm:text-lg sm:leading-6 resize-none"
                            placeholder="Contoh: Jadwalkan meeting review Q3 besok jam 2 siang, prioritas tinggi..."></textarea>
                    </div>

                    <!-- Footer Area Input -->
                    <div
                        class="bg-zinc-50 dark:bg-zinc-800/50 px-6 py-3 flex items-center justify-between border-t border-zinc-100 dark:border-zinc-800">
                        <p class="text-xs text-zinc-400">
                            Tekan <kbd
                                class="font-sans font-semibold text-zinc-500 border border-zinc-300 dark:border-zinc-600 rounded px-1">Enter</kbd>
                            untuk memproses
                        </p>

                        <button type="submit"
                            class="inline-flex items-center gap-x-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all disabled:opacity-50 disabled:cursor-not-allowed">

                            <!-- Loading Spinner -->
                            <svg wire:loading wire:target="processPrompt" class="animate-spin h-4 w-4 text-white"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>

                            <!-- Icon Sparkles -->
                            <svg wire:loading.remove wire:target="processPrompt" class="h-4 w-4" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                            </svg>

                            <span wire:loading.remove wire:target="processPrompt">Generate Task</span>
                            <span wire:loading wire:target="processPrompt">Thinking...</span>
                        </button>
                    </div>
                </form>
            </div>


        </div>

        <!-- Flash Message -->
        @if (session()->has('message'))
            <div
                class="mt-4 p-4 rounded-lg bg-green-50 dark:bg-green-500/10 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-500/20 flex items-center animate-fade-in-up">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="font-medium text-sm">{{ session('message') }}</span>
            </div>
        @endif

        <!-- TASKS GRID -->
        <div class="space-y-6">
            <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-4">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Your Tasks</h2>
                <span class="text-sm text-zinc-500">{{ $this->tasks->count() }} Tasks</span>
            </div>

            @if ($this->tasks->isEmpty())
                <div
                    class="text-center py-16 px-6 rounded-2xl border-2 border-dashed border-zinc-300 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50">
                    <svg class="mx-auto h-12 w-12 text-zinc-400" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <h3 class="mt-2 text-sm font-semibold text-zinc-900 dark:text-white">No tasks yet</h3>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Mulai dengan mengetik perintah ke AI di
                        atas.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($this->tasks as $task)
                        <div wire:click="selectTask({{ $task->id }})"
                            class="group relative bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-md hover:border-indigo-500/30 transition-all duration-200 flex flex-col cursor-pointer {{ $task->status == 'completed' ? 'opacity-60 grayscale-[0.5]' : '' }}">

                            <div class="p-5 flex-1 space-y-4">
                                <!-- Header Card -->
                                <div class="flex items-start justify-between">
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded text-[10px] font-medium uppercase tracking-wider border {{ $this->getPriorityClasses($task->priority) }}">
                                        {{ $task->priority }}
                                    </span>

                                    <!-- PENTING: Gunakan wire:click.stop pada checkbox agar tidak membuka modal saat dicentang -->
                                    <label
                                        class="relative flex items-center p-1 rounded-full cursor-pointer hover:bg-zinc-100 dark:hover:bg-zinc-800 transition z-10">
                                        <input type="checkbox" wire:click.stop="toggleStatus({{ $task->id }})"
                                            {{ $task->status == 'completed' ? 'checked' : '' }}
                                            class="w-5 h-5 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-600 dark:border-zinc-600 dark:bg-zinc-800 dark:checked:bg-indigo-500 cursor-pointer">
                                    </label>
                                </div>

                                <!-- Body Card -->
                                <div>
                                    <h3
                                        class="text-base font-semibold text-zinc-900 dark:text-zinc-100 leading-snug {{ $task->status == 'completed' ? 'line-through decoration-zinc-400' : '' }}">
                                        {{ $task->title }}
                                    </h3>
                                    <!-- Deskripsi dipotong (line-clamp) -->
                                    <p
                                        class="mt-2 text-sm text-zinc-500 dark:text-zinc-400 line-clamp-3 leading-relaxed">
                                        {{ $task->description }}
                                    </p>
                                </div>
                            </div>

                            <!-- Footer Card -->
                            <div
                                class="px-5 py-3 border-t border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/50 rounded-b-xl flex items-center gap-2 text-xs">
                                <svg class="w-4 h-4 {{ $task->due_date?->isPast() && $task->status != 'completed' ? 'text-red-500' : 'text-zinc-400' }}"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>

                                @if ($task->due_date)
                                    <span
                                        class="font-medium {{ $task->due_date->isPast() && $task->status != 'completed' ? 'text-red-600 dark:text-red-400' : 'text-zinc-500 dark:text-zinc-400' }}">
                                        {{ $task->due_date->format('d M Y • H:i') }}
                                    </span>
                                @else
                                    <span class="text-zinc-400 italic">No Due Date</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    <!-- TASK DETAIL MODAL -->
    @if ($selectedTask)
        <div class="relative z-50" role="dialog" aria-modal="true" x-data @keydown.escape.window="$wire.closeTask()">
            <!-- Backdrop (Dark Overlay) -->
            <div class="fixed inset-0 bg-zinc-900/80 backdrop-blur-sm transition-opacity" wire:click="closeTask"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

            <!-- Modal Panel -->
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-zinc-900 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-zinc-200 dark:border-zinc-700"
                        x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                        <!-- Modal Header -->
                        <div
                            class="bg-zinc-50 dark:bg-zinc-800/50 px-4 py-3 sm:px-6 flex justify-between items-center border-b border-zinc-100 dark:border-zinc-800">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $this->getPriorityClasses($selectedTask->priority) }}">
                                {{ $selectedTask->priority }} Priority
                            </span>

                            <!-- Close Button -->
                            <button wire:click="closeTask"
                                class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <div class="px-4 py-5 sm:p-6">
                            <div class="mb-4">
                                <h3 class="text-xl font-bold leading-6 text-zinc-900 dark:text-white mb-2">
                                    {{ $selectedTask->title }}
                                </h3>
                                <div class="flex items-center text-sm text-zinc-500 dark:text-zinc-400 gap-2">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    @if ($selectedTask->due_date)
                                        <span>Due: {{ $selectedTask->due_date->format('l, d F Y \a\t H:i') }}</span>
                                    @else
                                        <span>No Due Date</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Full Description -->
                            <div
                                class="prose prose-sm dark:prose-invert max-w-none text-zinc-600 dark:text-zinc-300 bg-zinc-50 dark:bg-zinc-950/50 p-4 rounded-lg border border-zinc-100 dark:border-zinc-800">
                                <p class="whitespace-pre-line">
                                    {{ $selectedTask->description ?? 'Tidak ada deskripsi tambahan.' }}</p>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div
                            class="bg-zinc-50 dark:bg-zinc-800/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2 border-t border-zinc-100 dark:border-zinc-800">
                            @if ($selectedTask->status == 'pending')
                                <button wire:click="toggleStatus({{ $selectedTask->id }})" type="button"
                                    class="inline-flex w-full justify-center rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:w-auto transition">
                                    Mark as Completed
                                </button>
                            @else
                                <button wire:click="toggleStatus({{ $selectedTask->id }})" type="button"
                                    class="inline-flex w-full justify-center rounded-lg bg-white dark:bg-zinc-700 px-3 py-2 text-sm font-semibold text-zinc-900 dark:text-white shadow-sm ring-1 ring-inset ring-zinc-300 dark:ring-zinc-600 hover:bg-zinc-50 dark:hover:bg-zinc-600 sm:w-auto transition">
                                    Mark as Pending
                                </button>
                            @endif

                            <button wire:click="deleteTask" wire:confirm="Yakin ingin menghapus tugas ini?"
                                type="button"
                                class="mt-3 inline-flex w-full justify-center rounded-lg bg-red-50 dark:bg-red-900/20 px-3 py-2 text-sm font-semibold text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/40 sm:mt-0 sm:w-auto transition">
                                Delete Task
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
