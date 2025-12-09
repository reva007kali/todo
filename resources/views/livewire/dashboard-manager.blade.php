<div class="min-h-screen text-zinc-900 dark:text-zinc-100 font-sans pb-20">
    <div class="max-w-4xl mx-auto space-y-10">

        <!-- HEADER & STATS -->
        <div class="space-y-6">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">
                        Task Manager
                    </h1>
                    <p class="mt-2 text-zinc-500 dark:text-zinc-400 text-sm">
                        Kelola produktivitasmu dengan bantuan AI atau manual.
                    </p>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-3 gap-4">
                <div
                    class="bg-white dark:bg-zinc-900 p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 shadow-sm flex flex-col items-center justify-center">
                    <span
                        class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $this->stats['total'] }}</span>
                    <span class="text-xs font-medium text-zinc-500 uppercase tracking-wider">Total Tasks</span>
                </div>
                <div
                    class="bg-white dark:bg-zinc-900 p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 shadow-sm flex flex-col items-center justify-center">
                    <span
                        class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $this->stats['pending'] }}</span>
                    <span class="text-xs font-medium text-zinc-500 uppercase tracking-wider">Pending</span>
                </div>
                <div
                    class="bg-white dark:bg-zinc-900 p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 shadow-sm flex flex-col items-center justify-center">
                    <span
                        class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $this->stats['completed'] }}</span>
                    <span class="text-xs font-medium text-zinc-500 uppercase tracking-wider">Completed</span>
                </div>
            </div>
        </div>

        <!-- NEW: FILTER TABS (MY TASKS vs SHARED) -->
        <div class="flex justify-center">
            <div class="bg-zinc-100 dark:bg-zinc-800 p-1 rounded-lg flex gap-1">
                <button wire:click="$set('viewFilter', 'my_tasks')"
                    class="px-4 py-2 text-sm font-medium rounded-md transition {{ $viewFilter === 'my_tasks' ? 'bg-white dark:bg-zinc-600 shadow text-indigo-600 dark:text-indigo-300' : 'text-zinc-500 hover:text-zinc-700 dark:text-zinc-400' }}">
                    Tugas Saya
                </button>
                <button wire:click="$set('viewFilter', 'shared')"
                    class="px-4 py-2 text-sm font-medium rounded-md transition {{ $viewFilter === 'shared' ? 'bg-white dark:bg-zinc-600 shadow text-indigo-600 dark:text-indigo-300' : 'text-zinc-500 hover:text-zinc-700 dark:text-zinc-400' }}">
                    Dibagikan ke Saya
                </button>
            </div>
        </div>

        <!-- INPUT SECTION (AI & MANUAL TOGGLE) - HANYA MUNCUL DI MY TASKS -->
        @if ($viewFilter === 'my_tasks')
            <div class="space-y-4">
                <!-- AI Input -->
                <div class="relative group">
                    <div
                        class="absolute -inset-0.5 bg-gradient-to-r from-indigo-500 to-blue-500 rounded-2xl opacity-20 blur group-hover:opacity-40 transition duration-1000">
                    </div>
                    <div
                        class="relative bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-600 shadow-sm overflow-hidden">
                        <form wire:submit.prevent="processPrompt" class="flex flex-col">
                            <div class="p-4">
                                <label for="prompt"
                                    class="block text-xs font-bold uppercase tracking-wider text-zinc-400 mb-2">
                                    Buat task baru dengan ai
                                </label>
                                <textarea wire:model="userPrompt" id="prompt" rows="2"
                                    class="block focus:outline-none w-full border-0 bg-transparent p-0 text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-600 focus:ring-0 sm:text-sm sm:leading-6 resize-none"
                                    placeholder="Contoh: Task Desain poster untuk social media untuk di posting besok!"></textarea>
                            </div>
                            <div
                                class="bg-zinc-50 dark:bg-zinc-800/50 px-4 py-2 flex items-center justify-between border-t border-zinc-100 dark:border-zinc-800">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="inline-flex items-center rounded-md bg-indigo-400/10 px-2 py-1 text-xs font-medium text-indigo-400 ring-1 ring-inset ring-indigo-400/20">AI
                                        Ready</span>
                                </div>
                                <button type="submit"
                                    class="inline-flex items-center gap-x-2 rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-indigo-500 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span wire:loading.remove wire:target="processPrompt">Generate Task</span>
                                    <span wire:loading wire:target="processPrompt">Thinking...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Manual Toggle & Form -->
                <div>
                    <button wire:click="toggleCreate"
                        class="flex items-center gap-2 text-sm font-medium text-zinc-600 dark:text-zinc-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition mb-3">
                        <svg class="w-5 h-5 transition-transform duration-200 {{ $isCreating ? 'rotate-45 text-red-500' : '' }}"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        {{ $isCreating ? 'Batal' : 'Tambah Task Manual' }}
                    </button>

                    @if ($isCreating)
                        <div
                            class="bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-5 shadow-inner space-y-4 animate-fade-in-down">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="col-span-1 md:col-span-2">
                                    <label class="block text-xs font-medium text-zinc-500 mb-1">Judul Task</label>
                                    <input type="text" wire:model="createForm.title"
                                        class="w-full focus:outline-1 p-3 rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Apa yang perlu diselesaikan?">
                                    @error('createForm.title')
                                        <span class="text-xs text-red-500">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-zinc-500 mb-1">Prioritas</label>
                                    <select wire:model="createForm.priority"
                                        class="w-full focus:outline-1 p-3 rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="High">High Priority</option>
                                        <option value="Middle">Middle Priority</option>
                                        <option value="Low">Low Priority</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-zinc-500 mb-1">Tenggat Waktu</label>
                                    <input type="datetime-local" wire:model="createForm.due_date"
                                        class="w-full focus:outline-1 p-3 rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-sm focus:ring-indigo-500 focus:border-indigo-500 text-zinc-500">
                                </div>
                                <div class="col-span-1 md:col-span-2">
                                    <label class="block p-3 text-xs font-medium text-zinc-500 mb-1">Deskripsi
                                        (Opsional)</label>
                                    <textarea wire:model="createForm.description" rows="2"
                                        class="w-full p-3 focus:outline-1 rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                                </div>
                            </div>
                            <div class="flex justify-end">
                                <button wire:click="saveManualTask"
                                    class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-lg text-sm font-semibold transition shadow-sm">
                                    Simpan Task
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- TASK LIST (ACCORDION STYLE) -->
        <div class="space-y-4">
            <div class="flex items-center justify-between pb-2 border-b border-zinc-200 dark:border-zinc-800">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Daftar Tugas</h2>
                <!-- Helper Text -->
                <p class="text-xs text-zinc-400 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Klik kartu untuk edit detail
                </p>
            </div>

            @if ($this->tasks->isEmpty())
                <div
                    class="text-center py-12 rounded-xl border-2 border-dashed border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50">
                    <p class="text-zinc-500 dark:text-zinc-400 text-sm">
                        @if ($viewFilter === 'my_tasks')
                            Belum ada tugas. Mulai ketik di atas!
                        @else
                            Belum ada tugas yang dibagikan ke Anda.
                        @endif
                    </p>
                </div>
            @else
                <div class="flex flex-col gap-3">
                    @foreach ($this->tasks as $task)
                        <div wire:key="task-{{ $task->id }}"
                            class="bg-white dark:bg-zinc-900 rounded-xl border {{ $expandedTaskId === $task->id ? 'border-blue-500 ring-1 ring-blue-500 shadow-md' : 'border-zinc-200 dark:border-zinc-800 hover:border-indigo-300 dark:hover:border-blue-700' }} transition-all duration-200 overflow-hidden">

                            <!-- Card Header (Always Visible) -->
                            <div class="p-4 flex items-start gap-4 cursor-pointer {{ $task->status == 'completed' ? 'bg-zinc-50 dark:bg-zinc-900/80' : '' }}"
                                wire:click="toggleExpand({{ $task->id }})">

                                <!-- Checkbox (Stop Propagation agar tidak trigger accordion) -->
                                <div class="pt-1" wire:click.stop>
                                    <input type="checkbox" wire:click="toggleStatus({{ $task->id }})"
                                        {{ $task->status == 'completed' ? 'checked' : '' }}
                                        class="w-5 h-5 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-600 dark:border-zinc-600 dark:bg-zinc-800 cursor-pointer">
                                </div>

                                <!-- Main Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2 mb-1">
                                        <h3
                                            class="text-base font-medium truncate {{ $task->status == 'completed' ? 'text-zinc-400 line-through' : 'text-zinc-900 dark:text-white' }}">
                                            {{ $task->title }}
                                        </h3>

                                        <!-- Badges & Tools -->
                                        <div class="flex items-center gap-2 shrink-0">
                                            <!-- NEW: Shared Badge (Jika di tab Shared) -->
                                            @if ($viewFilter === 'shared')
                                                <span
                                                    class="text-[10px] bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300 px-2 py-0.5 rounded border border-purple-200 dark:border-purple-800 flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                    {{ substr($task->user->name, 0, 8) }}..
                                                </span>
                                            @endif

                                            <!-- NEW: Share Button (Hanya jika Pemilik) -->
                                            @if ($task->user_id === auth()->id())
                                                <button wire:click.stop="openShareModal({{ $task->id }})"
                                                    class="p-1 text-zinc-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition"
                                                    title="Bagikan Task">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                                    </svg>
                                                </button>
                                            @endif

                                            <span
                                                class="inline-flex items-center rounded-md px-2 py-1 text-[10px] font-medium ring-1 ring-inset {{ $this->getPriorityColor($task->priority) }}">
                                                {{ $task->priority }}
                                            </span>

                                            <!-- Chevron Icon (Rotates when open) -->
                                            <svg class="w-5 h-5 text-zinc-400 transition-transform duration-200 {{ $expandedTaskId === $task->id ? 'rotate-180' : '' }}"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>

                                    <!-- Subtitle Preview -->
                                    <div class="space-y-2 text-zinc-500 dark:text-zinc-400">
                                        <h4 class="truncate w-[300px] text-sm lg:w-full ">{{ $task->description }}
                                        </h4>
                                        @if ($task->due_date)
                                            <h4
                                                class="flex text-xs items-center gap-1 {{ $task->due_date->isPast() && $task->status != 'completed' ? 'text-red-500 font-medium' : '' }}">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                {{ $task->due_date->format('d M H:i') }}
                                            </h4>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Accordion Body (Editable Form) -->
                            @if ($expandedTaskId === $task->id)
                                <div
                                    class="bg-zinc-50 dark:bg-black/20 border-t border-zinc-100 dark:border-zinc-800 p-5 animate-fade-in">
                                    <div class="flex items-center justify-between mb-4">
                                        <span
                                            class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wide">Mode
                                            Edit</span>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <!-- Edit Title -->
                                        <div class="col-span-1 md:col-span-2">
                                            <label class="block text-xs font-medium text-zinc-500 mb-1">Judul</label>
                                            <input type="text" wire:model="editForm.title"
                                                class="w-full p-3 rounded bg-white dark:bg-zinc-800 border-zinc-300 dark:border-zinc-700 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        </div>

                                        <!-- Edit Priority -->
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-zinc-500 mb-1">Prioritas</label>
                                            <select wire:model="editForm.priority"
                                                class="w-full p-3 rounded bg-white dark:bg-zinc-800 border-zinc-300 dark:border-zinc-700 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                                <option value="High">High</option>
                                                <option value="Middle">Middle</option>
                                                <option value="Low">Low</option>
                                            </select>
                                        </div>

                                        <!-- Edit Due Date -->
                                        <div>
                                            <label class="block text-xs font-medium text-zinc-500 mb-1">Due
                                                Date</label>
                                            <input type="datetime-local" wire:model="editForm.due_date"
                                                class="w-full p-3 rounded bg-white dark:bg-zinc-800 border-zinc-300 dark:border-zinc-700 text-sm focus:ring-indigo-500 focus:border-indigo-500 text-zinc-500">
                                        </div>

                                        <!-- Edit Description -->
                                        <div class="col-span-1 md:col-span-2">
                                            <label
                                                class="block text-xs font-medium text-zinc-500 mb-1">Deskripsi</label>
                                            <textarea wire:model="editForm.description" rows="3"
                                                class="w-full p-3 rounded bg-white dark:bg-zinc-800 border-zinc-300 dark:border-zinc-700 text-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex items-center justify-between pt-2">
                                        <!-- Hapus Button: Hanya Muncul Jika Pemilik -->
                                        @if ($task->user_id === auth()->id())
                                            <button wire:click="confirmDelete({{ $task->id }})" type="button"
                                                class="text-xs text-red-500 hover:text-red-600 font-medium flex items-center gap-1 px-2 py-1 rounded hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Hapus
                                            </button>
                                        @else
                                            <span></span> <!-- Spacer agar tombol simpan tetap di kanan -->
                                        @endif

                                        <div class="flex gap-2">
                                            <button wire:click="toggleExpand({{ $task->id }})"
                                                class="px-3 py-1.5 text-xs font-medium text-zinc-600 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800 rounded-lg transition">
                                                Batal
                                            </button>
                                            <button wire:click="updateTask"
                                                class="px-4 py-1.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-lg shadow-sm transition flex items-center gap-2">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="3" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Simpan Perubahan
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Notification Toast -->
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2"
            class="fixed bottom-4 right-4 z-50 bg-zinc-900 text-white px-4 py-3 rounded-lg shadow-lg text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('message') }}
        </div>
    @endif

    <!-- ERROR Toast -->
    @if (session()->has('error'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2"
            class="fixed bottom-4 right-4 z-50 bg-red-600 text-white px-4 py-3 rounded-lg shadow-lg text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('error') }}
        </div>
    @endif


    <!-- DELETE CONFIRMATION MODAL -->
    @if ($confirmingDeleteId)
        <div class="relative z-[999]" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-zinc-900/70 backdrop-blur-sm transition-opacity" x-data
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-xl bg-white dark:bg-zinc-900 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-zinc-200 dark:border-zinc-800"
                        x-data @click.outside="$wire.cancelDelete()" @keydown.escape.window="$wire.cancelDelete()"
                        x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                        <div class="bg-white dark:bg-zinc-900 px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div
                                    class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                    <h3 class="text-base font-semibold leading-6 text-zinc-900 dark:text-white"
                                        id="modal-title">
                                        Hapus Tugas?
                                    </h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                            Apakah Anda yakin ingin menghapus tugas ini? Tindakan ini tidak dapat
                                            dibatalkan.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            class="bg-zinc-50 dark:bg-zinc-800/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2 border-t border-zinc-100 dark:border-zinc-800">
                            <button type="button" wire:click="deleteTask"
                                class="inline-flex w-full justify-center rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto transition-colors">
                                <span wire:loading.remove wire:target="deleteTask">Ya, Hapus</span>
                                <span wire:loading wire:target="deleteTask">Menghapus...</span>
                            </button>
                            <button type="button" wire:click="cancelDelete"
                                class="mt-3 inline-flex w-full justify-center rounded-lg bg-white dark:bg-zinc-700 px-3 py-2 text-sm font-semibold text-zinc-900 dark:text-white shadow-sm ring-1 ring-inset ring-zinc-300 dark:ring-zinc-600 hover:bg-zinc-50 dark:hover:bg-zinc-600 sm:mt-0 sm:w-auto transition-colors">
                                Batal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- NEW: SHARE MODAL -->
    @if ($shareModalOpen)
        <div class="relative z-[999]" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-zinc-900/70 backdrop-blur-sm transition-opacity" x-data
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                wire:click="$set('shareModalOpen', false)"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-xl bg-white dark:bg-zinc-900 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-zinc-200 dark:border-zinc-800"
                        x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                        <!-- Modal Content -->
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg font-semibold leading-6 text-zinc-900 dark:text-white mb-4">
                                Bagikan Tugas
                            </h3>

                            <!-- Form Share -->
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-medium text-zinc-500 mb-1">Email Pengguna</label>
                                    <div class="flex gap-2">
                                        <input type="email" wire:model="shareEmail" placeholder="teman@email.com"
                                            class="block w-full p-2.5 rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <select wire:model="sharePermission"
                                            class="p-2.5 rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="edit">Bisa Edit</option>
                                            <option value="view">Hanya Lihat</option>
                                        </select>
                                    </div>
                                    @error('shareEmail')
                                        <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <button wire:click="shareTask"
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg py-2.5 text-sm font-semibold transition shadow-sm">
                                    Undang
                                </button>
                            </div>

                            <!-- List User yg sudah dishare -->
                            @php
                                $currentTask = \App\Models\Task::find($taskToShareId);
                            @endphp
                            @if ($currentTask && $currentTask->sharedWith->isNotEmpty())
                                <div class="mt-6 border-t border-zinc-100 dark:border-zinc-800 pt-4">
                                    <h4 class="text-xs font-bold uppercase text-zinc-400 mb-3 tracking-wider">Akses
                                        Saat Ini</h4>
                                    <div class="space-y-3">
                                        @foreach ($currentTask->sharedWith as $user)
                                            <div
                                                class="flex items-center justify-between text-sm p-2 rounded-lg bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-100 dark:border-zinc-700">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 dark:bg-indigo-900/50 dark:text-indigo-300 flex items-center justify-center text-xs font-bold">
                                                        {{ substr($user->name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                                            {{ $user->name }}</div>
                                                        <div class="text-xs text-zinc-500">{{ $user->email }}</div>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-3">
                                                    <span
                                                        class="text-xs text-zinc-500 capitalize bg-zinc-200 dark:bg-zinc-700 px-2 py-0.5 rounded">{{ $user->pivot->permission }}</span>
                                                    <button wire:click="removeShare({{ $user->id }})"
                                                        class="text-zinc-400 hover:text-red-500 transition">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Footer -->
                        <div class="bg-zinc-50 dark:bg-zinc-800/50 px-4 py-3 sm:px-6 flex flex-row-reverse">
                            <button type="button" wire:click="$set('shareModalOpen', false)"
                                class="inline-flex w-full justify-center rounded-lg bg-white dark:bg-zinc-700 px-3 py-2 text-sm font-semibold text-zinc-900 dark:text-white shadow-sm ring-1 ring-inset ring-zinc-300 dark:ring-zinc-600 hover:bg-zinc-50 dark:hover:bg-zinc-600 sm:w-auto transition-colors">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
