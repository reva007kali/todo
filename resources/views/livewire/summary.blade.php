<div class="min-h-screen bg-zinc-50 dark:bg-[#09090b] rounded-lg lg:rounded-3xl font-sans pb-20">
    <div class="max-w-6xl mx-auto pt-10 px-4 sm:px-6 lg:px-8 space-y-8">

        <!-- HEADER -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Dashboard</h1>
                <p class="text-zinc-500 dark:text-zinc-400 text-sm">Welcome back, {{ auth()->user()->name }}</p>
            </div>
            <div class="text-right hidden sm:block">
                <p class="text-sm font-medium text-zinc-900 dark:text-zinc-200">{{ now()->format('l, d F Y') }}</p>
            </div>
        </div>

        <!-- SECTION 1: METRICS & AI BRIEFING -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Card 1: Completed Stats -->
            <div
                class="bg-white dark:bg-zinc-900 rounded-xl p-6 border border-zinc-200 dark:border-zinc-800 shadow-sm flex flex-col justify-center items-center lg:items-start relative overflow-hidden group">
                <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition">
                    <svg class="w-24 h-24 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <h3 class="text-zinc-500 dark:text-zinc-400 text-sm font-medium uppercase tracking-wider">Tasks
                    Completed</h3>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-4xl font-bold text-zinc-900 dark:text-white">{{ $this->completedCount }}</span>
                    <span class="text-sm text-green-600 dark:text-green-400 font-medium">All time</span>
                </div>
            </div>

            <!-- Card 2: AI Daily Briefing (Wide) -->
            <div
                class="lg:col-span-2 bg-gradient-to-br from-indigo-600 to-purple-700 rounded-xl p-6 text-white shadow-lg relative overflow-hidden">
                <!-- Decorative Circle -->
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-white opacity-10 rounded-full blur-2xl"></div>

                <div class="relative z-10 h-full flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start">
                            <h3 class="flex items-center gap-2 font-bold text-lg">
                                <svg class="w-5 h-5 text-indigo-200" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                AI Daily Briefing
                            </h3>
                            @if (!$aiSummary)
                                <button wire:click="generateDailyBriefing" wire:loading.attr="disabled"
                                    wire:target="generateDailyBriefing"
                                    class="bg-white/20 hover:bg-white/30 text-xs px-3 py-1 rounded-full backdrop-blur-sm transition flex items-center gap-2 disabled:opacity-50 disabled:cursor-wait">
                                    <!-- Tampil saat normal -->
                                    <span wire:loading.remove wire:target="generateDailyBriefing">
                                        Generate Briefing
                                    </span>

                                    <!-- Tampil saat loading -->
                                    <span wire:loading wire:target="generateDailyBriefing"
                                        class="flex items-center gap-1">
                                        <svg class="animate-spin h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        Analyzing...
                                    </span>
                                </button>
                            @endif
                        </div>

                        <div class="mt-3 text-indigo-50 dark:text-indigo-100 text-sm leading-relaxed min-h-[60px]">
                            @if ($isGeneratingSummary)
                                <div class="flex items-center gap-2 animate-pulse">
                                    <div class="w-2 h-2 bg-white rounded-full animate-bounce"></div>
                                    <div class="w-2 h-2 bg-white rounded-full animate-bounce delay-75"></div>
                                    <div class="w-2 h-2 bg-white rounded-full animate-bounce delay-150"></div>
                                    <span>AI sedang menganalisa tugas Anda...</span>
                                </div>
                            @elseif($aiSummary)
                                <!-- PERBAIKAN DISINI: Gunakan untuk render HTML -->
                                <!-- Class 'prose' dari Tailwind Typography akan mempercantik elemen <p>, <ul>, <strong> otomatis -->
                                <div class="prose prose-sm prose-invert max-w-none text-indigo-50">
                                    {!! $aiSummary !!}
                                </div>
                            @else
                                <p class="opacity-80">Klik tombol generate untuk mendapatkan ringkasan strategi hari ini
                                    berdasarkan tugas pending Anda.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 2: CREATE TASK INPUT -->
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 p-1 shadow-sm">
            <form wire:submit.prevent="processPrompt"
                class="relative bg-zinc-50 dark:bg-[#121215] rounded-lg p-4 transition-all focus-within:ring-2 focus-within:ring-indigo-500/20">
                <div class="flex items-start gap-4">
                    <div class="mt-1 flex-shrink-0">
                        <div
                            class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <textarea wire:model="userPrompt" rows="2"
                            class="block w-full border-0 bg-transparent p-0 text-zinc-900 dark:text-white placeholder:text-zinc-400 focus:ring-0 sm:text-sm resize-none"
                            placeholder="Apa yang ingin Anda kerjakan? (Contoh: Siapkan laporan marketing besok, prioritas tinggi...)"></textarea>
                    </div>
                    <div class="flex-shrink-0">
                        <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg p-2 transition shadow-sm"
                            wire:loading.attr="disabled">
                            <svg wire:loading.remove wire:target="processPrompt" class="w-5 h-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                            <svg wire:loading wire:target="processPrompt" class="w-5 h-5 animate-spin" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- SECTION 3: TWO COLUMNS (FOCUS & LATEST) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            <!-- LEFT: IMPORTANT TASKS -->
            <div class="space-y-4">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-1.5 h-6 bg-red-500 rounded-full"></div>
                    <h2 class="text-lg font-bold text-zinc-900 dark:text-white">High Priority Focus</h2>
                    <span
                        class="bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400 text-xs px-2 py-0.5 rounded-full border border-red-200 dark:border-red-500/20 font-medium">Do
                        First</span>
                </div>

                @if ($this->importantTasks->isEmpty())
                    <div
                        class="text-center py-8 bg-white dark:bg-zinc-900 rounded-xl border border-dashed border-zinc-200 dark:border-zinc-800">
                        <p class="text-zinc-500 text-sm">Tidak ada tugas Urgent saat ini.</p>
                    </div>
                @else
                    <div class="flex flex-col gap-3">
                        @foreach ($this->importantTasks as $task)
                            <div
                                class="bg-white dark:bg-zinc-900 p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-md hover:border-red-500/30 transition group">
                                <div class="flex justify-between items-start">
                                    <div class="flex items-start gap-3">
                                        <input type="checkbox" wire:click="toggleStatus({{ $task->id }})"
                                            class="mt-1 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                        <div>
                                            <h4
                                                class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 group-hover:text-red-600 dark:group-hover:text-red-400 transition">
                                                {{ $task->title }}</h4>
                                            <p class="text-xs text-zinc-500 mt-1 line-clamp-1">
                                                {{ $task->description ?? 'No details' }}</p>
                                        </div>
                                    </div>
                                    <div
                                        class="text-xs text-red-600 font-medium whitespace-nowrap bg-red-50 dark:bg-red-900/10 px-2 py-1 rounded">
                                        {{ $task->due_date ? $task->due_date->format('d M') : 'ASAP' }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- RIGHT: LATEST TASKS -->
            <div class="space-y-4">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-1.5 h-6 bg-indigo-500 rounded-full"></div>
                    <h2 class="text-lg font-bold text-zinc-900 dark:text-white">Recent Activity</h2>
                </div>

                @if ($this->latestTasks->isEmpty())
                    <div
                        class="text-center py-8 bg-white dark:bg-zinc-900 rounded-xl border border-dashed border-zinc-200 dark:border-zinc-800">
                        <p class="text-zinc-500 text-sm">Belum ada aktivitas.</p>
                    </div>
                @else
                    <div class="relative border-l border-zinc-200 dark:border-zinc-800 ml-3 space-y-6">
                        @foreach ($this->latestTasks as $task)
                            <div class="relative pl-6">
                                <!-- Timeline Dot -->
                                <div
                                    class="absolute -left-1.5 top-1.5 w-3 h-3 rounded-full bg-zinc-200 dark:bg-zinc-700 ring-4 ring-zinc-50 dark:ring-[#09090b]">
                                </div>

                                <div
                                    class="bg-white dark:bg-zinc-900 p-3 rounded-lg border border-zinc-200 dark:border-zinc-800 shadow-sm flex justify-between items-center">
                                    <div>
                                        <p class="text-sm font-medium text-zinc-900 dark:text-zinc-200">
                                            {{ $task->title }}</p>
                                        <p class="text-xs text-zinc-500">{{ $task->created_at->diffForHumans() }}</p>
                                    </div>
                                    <span
                                        class="px-2 py-0.5 rounded text-[10px] border uppercase {{ $this->getPriorityColor($task->priority) }}">
                                        {{ $task->priority }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- LINK TO FULL LIST -->
        <div class="flex justify-center pt-4">
            <a href="{{ url('/tasks') }}"
                class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
                View All Tasks
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>

    </div>
</div>
