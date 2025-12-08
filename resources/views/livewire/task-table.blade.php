<div class="space-y-6">

    <!-- HEADER & FILTERS SECTION -->
    <div class="space-y-4">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="text-xl font-bold text-zinc-900 dark:text-white">All Tasks</h2>
            
            <!-- SEARCH BAR (Mencari Judul & Deskripsi) -->
            <div class="relative w-full md:w-80">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text"
                    class="block w-full rounded-xl border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 py-2.5 pl-10 pr-3 text-sm placeholder:text-zinc-400 focus:border-indigo-500 focus:ring-indigo-500 dark:text-zinc-200 dark:focus:ring-indigo-500/50 shadow-sm"
                    placeholder="Cari judul atau deskripsi...">
            </div>
        </div>

        <!-- FILTER CONTROLS (Easy Click) -->
        <div class="flex flex-col md:flex-row gap-4 md:items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-4">
            
            <!-- STATUS FILTER (Tabs Style) -->
            <div class="flex items-center gap-1 bg-zinc-100 dark:bg-zinc-800/50 p-1 rounded-lg self-start">
                @foreach(['all' => 'All', 'pending' => 'Pending', 'completed' => 'Done'] as $key => $label)
                    <button wire:click="$set('filterStatus', '{{ $key }}')"
                        class="px-4 py-1.5 text-xs font-medium rounded-md transition-all duration-200 
                        {{ $filterStatus === $key 
                            ? 'bg-white dark:bg-zinc-700 text-indigo-600 dark:text-indigo-400 shadow-sm ring-1 ring-zinc-200 dark:ring-zinc-600' 
                            : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <!-- PRIORITY FILTER (Colored Pills) -->
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xs font-medium text-zinc-400 uppercase tracking-wider mr-1">Priority:</span>
                
                <button wire:click="$set('filterPriority', 'all')"
                    class="px-3 py-1 rounded-full text-xs font-medium border transition-all
                    {{ $filterPriority === 'all' 
                        ? 'bg-zinc-800 text-white border-zinc-800 dark:bg-white dark:text-zinc-900' 
                        : 'bg-white text-zinc-600 border-zinc-200 hover:border-zinc-300 dark:bg-zinc-900 dark:text-zinc-400 dark:border-zinc-700' }}">
                    All
                </button>

                <!-- High -->
                <button wire:click="$set('filterPriority', 'High')"
                    class="px-3 py-1 rounded-full text-xs font-medium border transition-all
                    {{ $filterPriority === 'High' 
                        ? 'bg-red-100 text-red-700 border-red-200 ring-1 ring-red-500/20' 
                        : 'bg-white text-zinc-600 border-zinc-200 hover:border-red-200 hover:text-red-600 dark:bg-zinc-900 dark:text-zinc-400 dark:border-zinc-700 dark:hover:border-red-900/50' }}">
                    High
                </button>

                <!-- Middle -->
                <button wire:click="$set('filterPriority', 'Middle')"
                    class="px-3 py-1 rounded-full text-xs font-medium border transition-all
                    {{ $filterPriority === 'Middle' 
                        ? 'bg-yellow-100 text-yellow-700 border-yellow-200 ring-1 ring-yellow-500/20' 
                        : 'bg-white text-zinc-600 border-zinc-200 hover:border-yellow-200 hover:text-yellow-600 dark:bg-zinc-900 dark:text-zinc-400 dark:border-zinc-700 dark:hover:border-yellow-900/50' }}">
                    Middle
                </button>

                <!-- Low -->
                <button wire:click="$set('filterPriority', 'Low')"
                    class="px-3 py-1 rounded-full text-xs font-medium border transition-all
                    {{ $filterPriority === 'Low' 
                        ? 'bg-blue-100 text-blue-700 border-blue-200 ring-1 ring-blue-500/20' 
                        : 'bg-white text-zinc-600 border-zinc-200 hover:border-blue-200 hover:text-blue-600 dark:bg-zinc-900 dark:text-zinc-400 dark:border-zinc-700 dark:hover:border-blue-900/50' }}">
                    Low
                </button>
            </div>
        </div>
    </div>

    <!-- TABLE CONTAINER (Sedikit update di Shadow dan Border) -->
    <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer group hover:bg-zinc-100 dark:hover:bg-zinc-800 transition" wire:click="sortBy('title')">
                            <div class="flex items-center gap-1">
                                Title
                                @if($sortField === 'title')
                                    <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? 'rotate-180' : '' }}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer group hover:bg-zinc-100 dark:hover:bg-zinc-800 transition" wire:click="sortBy('priority')">
                             <div class="flex items-center gap-1">
                                Priority
                                @if($sortField === 'priority')
                                    <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? 'rotate-180' : '' }}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer group hover:bg-zinc-100 dark:hover:bg-zinc-800 transition" wire:click="sortBy('due_date')">
                             <div class="flex items-center gap-1">
                                Due Date
                                @if($sortField === 'due_date')
                                    <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? 'rotate-180' : '' }}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer group hover:bg-zinc-100 dark:hover:bg-zinc-800 transition" wire:click="sortBy('status')">
                            Status
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                    @forelse ($tasks as $task)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition duration-150">
                            <!-- Title & Desc -->
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium text-zinc-900 dark:text-white {{ $task->status == 'completed' ? 'line-through text-zinc-400 dark:text-zinc-600' : '' }}">
                                        {{ $task->title }}
                                    </span>
                                    @if($task->description)
                                    <span class="text-xs text-zinc-500 dark:text-zinc-400 line-clamp-1 max-w-xs mt-0.5">
                                        {{ $task->description }}
                                    </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Priority -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $this->getPriorityClasses($task->priority) }}">
                                    {{ $task->priority }}
                                </span>
                            </td>

                            <!-- Due Date -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                @if ($task->due_date)
                                    <span class="{{ $task->due_date->isPast() && $task->status != 'completed' ? 'text-red-600 font-medium' : '' }}">
                                        {{ $task->due_date->format('d M Y') }}
                                    </span>
                                @else
                                    <span class="text-zinc-400 italic text-xs">No Date</span>
                                @endif
                            </td>

                            <!-- Status Toggle -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button wire:click="toggleStatus({{ $task->id }})"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500
                                    {{ $task->status == 'completed'
                                        ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20'
                                        : 'bg-zinc-100 text-zinc-700 border border-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:border-zinc-700 hover:bg-zinc-200 dark:hover:bg-zinc-700' }}">
                                    @if ($task->status == 'completed')
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                        Done
                                    @else
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        Pending
                                    @endif
                                </button>
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button wire:click="deleteTask({{ $task->id }})"
                                    wire:confirm="Hapus tugas ini secara permanen?"
                                    class="text-zinc-400 hover:text-red-600 dark:hover:text-red-400 transition">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-zinc-500 dark:text-zinc-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-zinc-300 dark:text-zinc-600 mb-2" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    <p class="text-sm font-medium">Tidak ada tugas ditemukan</p>
                                    <p class="text-xs text-zinc-400">Coba ubah filter atau kata kunci pencarian.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        <div class="border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50 px-6 py-3">
            {{ $tasks->links() }}
        </div>
    </div>
</div>