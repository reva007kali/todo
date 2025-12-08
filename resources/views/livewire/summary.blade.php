<div class="min-h-screen dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 font-sans pb-20">

    <!-- Include Chart.js (Bisa dipindah ke layout utama) -->


    <div class="max-w-5xl mx-auto pt-8 px-4 sm:px-6 lg:px-8 space-y-8">

        <!-- HEADER SIMPLE -->
        <div
            class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-zinc-200 dark:border-zinc-800 pb-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Executive Summary</h1>
                <p class="text-zinc-500 dark:text-zinc-400 text-sm mt-1">
                    Analisis performa & wawasan tugas Anda per {{ now()->format('d F Y') }}
                </p>
            </div>
            <button wire:click="generateInsight" wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm disabled:opacity-70 disabled:cursor-wait">
                <svg wire:loading.remove wire:target="generateInsight" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <svg wire:loading wire:target="generateInsight" class="w-4 h-4 animate-spin" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <span wire:loading.remove wire:target="generateInsight">Generate AI Insight</span>
                <span wire:loading wire:target="generateInsight">Analyzing...</span>
            </button>
        </div>

        <!-- AI INSIGHT SECTION -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Summary Card -->
    <div
        class="md:col-span-2 bg-white dark:bg-zinc-900 rounded-2xl p-6 border border-zinc-200 dark:border-zinc-800 shadow-sm relative overflow-hidden">
        <h3 class="text-sm font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-3">
            AI Task Analysis
        </h3>
        <div class="relative z-10 min-h-[80px]">
            @if ($aiAnalysis)
                <!-- PERUBAHAN DI SINI -->
                <!-- Kita gunakan agar HTML dari AI dirender browser -->
                <!-- class 'prose' akan otomatis styling h1, p, ul, li, strong, dll -->
                <div class="prose prose-sm prose-zinc dark:prose-invert max-w-none text-sm leading-relaxed animate-fade-in">
                    {!! $aiAnalysis !!}
                </div>
            @else
                <p class="text-zinc-500 italic text-sm">
                    Klik tombol "Generate AI Insight" untuk mendapatkan ringkasan prioritas tugas Anda.
                </p>
            @endif
        </div>
    </div>

    <!-- Productivity Tip Card (Tidak berubah) -->
    <div
        class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden flex flex-col justify-center">
        <!-- ... code productivity tip ... -->
        <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-white opacity-10 rounded-full blur-xl"></div>
        <h3 class="text-xs font-bold uppercase tracking-wider text-indigo-200 mb-2 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
            </svg>
            Daily Tip
        </h3>
        <p class="text-sm font-medium leading-relaxed italic opacity-90">
            "{{ $productivityTip ?? 'Lakukan tugas tersulit di pagi hari saat energi Anda masih penuh (Eat the Frog).' }}"
        </p>
    </div>
</div>

        <!-- CHARTS SECTION -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            <!-- 1. STATUS CHART (Doughnut) -->
            <div
                class="bg-white dark:bg-zinc-900 p-5 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm flex flex-col items-center">
                <h4 class="text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-4 self-start">Task Completion
                </h4>
                <div class="relative w-40 h-40" x-data="{
                    initChart() {
                        new Chart(this.$refs.canvas, {
                            type: 'doughnut',
                            data: {
                                labels: @json($this->statusChartData['labels']),
                                datasets: [{
                                    data: @json($this->statusChartData['data']),
                                    backgroundColor: ['#e4e4e7', '#10b981'],
                                    /* Zinc-200, Emerald-500 */
                                    borderWidth: 0
                                }]
                            },
                            options: { cutout: '75%', plugins: { legend: { display: false } } }
                        });
                    }
                }" x-init="initChart">
                    <canvas x-ref="canvas"></canvas>
                    <!-- Center Text -->
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <span
                            class="text-2xl font-bold text-zinc-800 dark:text-zinc-200">{{ $this->statusChartData['data'][1] }}</span>
                    </div>
                </div>
                <div class="flex gap-4 mt-4 text-xs text-zinc-500">
                    <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Done
                    </div>
                    <div class="flex items-center gap-1"><span
                            class="w-2 h-2 rounded-full bg-zinc-200 dark:bg-zinc-700"></span> Pending</div>
                </div>
            </div>

            <!-- 2. URGENCY CHART (Doughnut) -->
            <div
                class="bg-white dark:bg-zinc-900 p-5 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm flex flex-col items-center">
                <h4 class="text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-4 self-start">Pending Urgency
                </h4>
                <div class="relative w-40 h-40" x-data="{
                    initChart() {
                        new Chart(this.$refs.canvas, {
                            type: 'doughnut',
                            data: {
                                labels: @json($this->priorityChartData['labels']),
                                datasets: [{
                                    data: @json($this->priorityChartData['data']),
                                    backgroundColor: ['#ef4444', '#f59e0b', '#3b82f6'],
                                    /* Red, Amber, Blue */
                                    borderWidth: 0
                                }]
                            },
                            options: { cutout: '75%', plugins: { legend: { display: false } } }
                        });
                    }
                }" x-init="initChart">
                    <canvas x-ref="canvas"></canvas>
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <span
                            class="text-2xl font-bold text-zinc-800 dark:text-zinc-200">{{ array_sum($this->priorityChartData['data']) }}</span>
                    </div>
                </div>
                <div class="flex gap-3 mt-4 text-[10px] text-zinc-500">
                    <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-500"></span> High
                    </div>
                    <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-500"></span> Mid
                    </div>
                    <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-500"></span> Low
                    </div>
                </div>
            </div>

            <!-- 3. PRODUCTIVITY LINE CHART (Fixed) -->
          

        </div>

        <!-- FOOTER INFO -->
        <div class="text-center pt-8 border-t border-zinc-200 dark:border-zinc-800">
            <p class="text-xs text-zinc-400">
                Data diperbarui secara real-time. Chart menggunakan Chart.js.
            </p>
        </div>

    </div>
</div>
