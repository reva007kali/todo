<div class="space-y-6">

    <!-- HEADER & FILTERS -->
    <div
        class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-4 md:p-6 shadow-sm space-y-5">

        <!-- Top: Title & Search -->
        <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
            <div>
                <h2 class="text-xl font-bold text-zinc-900 dark:text-white tracking-tight">Semua Tugas</h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Kelola dan pantau semua aktivitasmu.</p>
            </div>

            <div class="relative w-full md:w-72 group">
                <div
                    class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-zinc-400 group-focus-within:text-indigo-500 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text"
                    class="block w-full rounded-xl border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50 py-2.5 pl-10 pr-3 text-sm placeholder:text-zinc-400 focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white dark:focus:bg-zinc-900 transition-all shadow-sm"
                    placeholder="Cari tugas...">
            </div>
        </div>

        <!-- Bottom: Filters -->
        <div
            class="flex flex-col md:flex-row gap-4 md:items-center justify-between border-t border-zinc-100 dark:border-zinc-800 pt-4">

            <!-- Tabs Status -->
            <div
                class="flex items-center p-1 bg-zinc-100 dark:bg-zinc-800 rounded-lg self-start md:self-auto overflow-x-auto max-w-full">
                @foreach (['all' => 'Semua', 'pending' => 'Pending', 'completed' => 'Selesai'] as $key => $label)
                    <button wire:click="$set('filterStatus', '{{ $key }}')"
                        class="px-4 py-1.5 text-xs font-semibold rounded-md transition-all whitespace-nowrap
                        {{ $filterStatus === $key
                            ? 'bg-white dark:bg-zinc-600 text-indigo-600 dark:text-indigo-300 shadow-sm ring-1 ring-black/5 dark:ring-white/10'
                            : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <!-- Priority Filters -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1 md:pb-0 scrollbar-hide">
                <span
                    class="text-xs font-medium text-zinc-400 uppercase tracking-wider hidden md:block">Prioritas:</span>

                @foreach (['all' => 'All', 'High' => '🔥 High', 'Middle' => '⚡ Mid', 'Low' => '☕ Low'] as $key => $label)
                    <button wire:click="$set('filterPriority', '{{ $key }}')"
                        class="px-3 py-1.5 rounded-full text-xs font-medium border transition-all whitespace-nowrap
                        {{ $filterPriority === $key
                            ? 'bg-zinc-900 text-white border-zinc-900 dark:bg-indigo-600 dark:border-indigo-500 dark:text-white'
                            : 'bg-white text-zinc-600 border-zinc-200 hover:border-zinc-300 hover:bg-zinc-50 dark:bg-zinc-900 dark:text-zinc-400 dark:border-zinc-700' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <!-- CONTENT AREA -->

    <!-- 1. MOBILE VIEW (Card Layout) - Visible only on mobile -->
    <div class="md:hidden space-y-3">
        @forelse ($tasks as $task)
            <div wire:click="openViewModal({{ $task->id }})"
                class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-sm active:scale-[0.98] transition-transform cursor-pointer">

                <div class="flex justify-between items-start mb-2">
                    <span
                        class="inline-flex items-center px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider border {{ $this->getPriorityClasses($task->priority) }}">
                        {{ $task->priority }}
                    </span>
                    @if ($task->due_date)
                        <span
                            class="text-xs {{ $task->due_date->isPast() && $task->status != 'completed' ? 'text-red-500 font-medium' : 'text-zinc-400' }}">
                            {{ $task->due_date->format('d M') }}
                        </span>
                    @endif
                </div>

                <h3
                    class="text-sm font-semibold text-zinc-900 dark:text-white mb-1 {{ $task->status == 'completed' ? 'line-through text-zinc-400' : '' }}">
                    {{ $task->title }}
                </h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 line-clamp-2 mb-3">
                    {{ $task->description ?: 'Tidak ada deskripsi tambahan.' }}
                </p>

                <div class="flex items-center justify-between border-t border-zinc-100 dark:border-zinc-800 pt-3">
                    <div class="flex items-center gap-2" wire:click.stop>
                        <button wire:click="toggleStatus({{ $task->id }})"
                            class="flex items-center gap-1.5 px-2 py-1 rounded-md text-xs font-medium transition-colors
                            {{ $task->status == 'completed'
                                ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400'
                                : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300' }}">
                            <div
                                class="w-2 h-2 rounded-full {{ $task->status == 'completed' ? 'bg-emerald-500' : 'bg-zinc-400' }}">
                            </div>
                            {{ $task->status == 'completed' ? 'Selesai' : 'Pending' }}
                        </button>
                    </div>
                    <svg class="w-4 h-4 text-zinc-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </div>
        @empty
            <div
                class="text-center py-10 bg-zinc-50 dark:bg-zinc-900 rounded-xl border border-dashed border-zinc-300 dark:border-zinc-700">
                <p class="text-sm text-zinc-500">Tidak ada tugas ditemukan.</p>
            </div>
        @endforelse
    </div>

    <!-- 2. DESKTOP VIEW (Table Layout) - Hidden on mobile -->
    <div
        class="hidden md:block overflow-hidden rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-sm">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
            <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                <tr>
                    <th scope="col"
                        class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-indigo-600 transition"
                        wire:click="sortBy('title')">
                        Judul & Deskripsi
                    </th>
                    <th scope="col"
                        class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-indigo-600 transition"
                        wire:click="sortBy('priority')">
                        Prioritas
                    </th>
                    <th scope="col"
                        class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-indigo-600 transition"
                        wire:click="sortBy('due_date')">
                        Tenggat Waktu
                    </th>
                    <th scope="col"
                        class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-indigo-600 transition"
                        wire:click="sortBy('status')">
                        Status
                    </th>
                    <th scope="col" class="relative px-6 py-4">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                @forelse ($tasks as $task)
                    <tr wire:click="openViewModal({{ $task->id }})"
                        class="group hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition duration-150 cursor-pointer">

                        <!-- Title & Desc -->
                        <td class="px-6 py-4 max-w-sm">
                            <div class="flex flex-col">
                                <span
                                    class="text-sm font-medium text-zinc-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors {{ $task->status == 'completed' ? 'line-through text-zinc-400 dark:text-zinc-600' : '' }}">
                                    {{ $task->title }}
                                </span>
                                @if ($task->description)
                                    <span class="text-xs text-zinc-500 dark:text-zinc-400 truncate mt-1">
                                        {{ $task->description }}
                                    </span>
                                @endif
                            </div>
                        </td>

                        <!-- Priority -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded text-xs font-bold border {{ $this->getPriorityClasses($task->priority) }}">
                                {{ $task->priority }}
                            </span>
                        </td>

                        <!-- Due Date -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if ($task->due_date)
                                <div
                                    class="flex items-center gap-1.5 text-sm {{ $task->due_date->isPast() && $task->status != 'completed' ? 'text-red-600 dark:text-red-400 font-medium' : 'text-zinc-500 dark:text-zinc-400' }}">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>{{ $task->due_date->format('d M Y') }}</span>
                                </div>
                            @else
                                <span class="text-zinc-400 text-xs italic">--</span>
                            @endif
                        </td>

                        <!-- Status (Stop Propagation) -->
                        <td class="px-6 py-4 whitespace-nowrap" wire:click.stop>
                            <button wire:click="toggleStatus({{ $task->id }})"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500
                                {{ $task->status == 'completed'
                                    ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200 dark:bg-emerald-500/20 dark:text-emerald-300 dark:hover:bg-emerald-500/30'
                                    : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700' }}">
                                <div
                                    class="w-1.5 h-1.5 rounded-full {{ $task->status == 'completed' ? 'bg-emerald-500' : 'bg-zinc-500' }}">
                                </div>
                                {{ $task->status == 'completed' ? 'Done' : 'Pending' }}
                            </button>
                        </td>

                        <!-- Actions (Stop Propagation) -->
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium" wire:click.stop>
                            <button wire:click="deleteTask({{ $task->id }})"
                                wire:confirm="Yakin ingin menghapus tugas ini?"
                                class="p-2 text-zinc-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                title="Hapus Tugas">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-zinc-500 dark:text-zinc-400">
                                <svg class="w-12 h-12 mb-3 text-zinc-300 dark:text-zinc-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <p class="text-base font-medium">Belum ada tugas</p>
                                <p class="text-sm text-zinc-400">Sesuaikan filter atau buat tugas baru.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $tasks->links() }}
    </div>

    <!-- 3. TASK DETAIL MODAL -->
    @if ($isViewModalOpen && $selectedTask)
        <div class="relative z-[100]" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-zinc-900/60 backdrop-blur-sm transition-opacity" wire:click="closeViewModal"
                x-data x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            </div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">

                    <!-- Modal Panel -->
                    <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-zinc-900 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-zinc-200 dark:border-zinc-800"
                        x-data x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                        <!-- Header -->
                        <div
                            class="bg-zinc-50 dark:bg-zinc-800/50 px-4 py-4 sm:px-6 flex justify-between items-center border-b border-zinc-100 dark:border-zinc-800">
                            <div class="flex items-center gap-2">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded text-[10px] font-bold border uppercase tracking-wider {{ $this->getPriorityClasses($selectedTask->priority) }}">
                                    {{ $selectedTask->priority }}
                                </span>
                                <span class="text-xs text-zinc-400">#ID {{ $selectedTask->id }}</span>
                            </div>
                            <button wire:click="closeViewModal"
                                class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 transition">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Body -->
                        <div class="px-4 py-6 sm:px-6 space-y-5">
                            <!-- Status Toggle (Interactive inside modal) -->
                            <div class="flex items-center justify-between">
                                <h3 class="text-xl font-bold text-zinc-900 dark:text-white leading-snug">
                                    {{ $selectedTask->title }}
                                </h3>
                            </div>

                            <!-- Date Info -->
                            <div
                                class="flex items-center gap-4 text-sm text-zinc-500 dark:text-zinc-400 bg-zinc-50 dark:bg-zinc-800/50 p-3 rounded-lg">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>{{ $selectedTask->due_date ? $selectedTask->due_date->format('l, d F Y H:i') : 'Tanpa Tenggat' }}</span>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="prose prose-sm dark:prose-invert max-w-none text-zinc-600 dark:text-zinc-300">
                                <p class="whitespace-pre-wrap">
                                    {{ $selectedTask->description ?: 'Tidak ada deskripsi detail untuk tugas ini.' }}
                                </p>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div
                            class="bg-zinc-50 dark:bg-zinc-800/30 px-4 py-4 sm:px-6 sm:flex sm:flex-row-reverse gap-3 border-t border-zinc-100 dark:border-zinc-800">
                            <button type="button" wire:click="toggleStatus({{ $selectedTask->id }})"
                                class="w-full inline-flex justify-center items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold shadow-sm sm:w-auto transition-all
                            {{ $selectedTask->status == 'completed'
                                ? 'bg-white border border-zinc-300 text-zinc-700 hover:bg-zinc-50 dark:bg-zinc-800 dark:border-zinc-600 dark:text-zinc-200'
                                : 'bg-indigo-600 text-white hover:bg-indigo-500' }}">
                                @if ($selectedTask->status == 'completed')
                                    <span>Tandai Belum Selesai</span>
                                @else
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>Selesaikan Tugas</span>
                                @endif
                            </button>

                            <button type="button" wire:click="deleteTask({{ $selectedTask->id }})"
                                wire:confirm="Hapus tugas ini?"
                                class="mt-3 sm:mt-0 w-full inline-flex justify-center rounded-lg bg-white dark:bg-zinc-800 px-4 py-2 text-sm font-semibold text-red-600 shadow-sm ring-1 ring-inset ring-zinc-300 dark:ring-zinc-600 hover:bg-red-50 dark:hover:bg-red-900/20 sm:w-auto transition-all">
                                Hapus
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
