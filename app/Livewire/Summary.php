<?php

namespace App\Livewire;

use App\Models\Task;
use Livewire\Component;
use App\Services\AIService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Summary extends Component
{
    public $aiAnalysis = null;
    public $productivityTip = null;
    public $isAnalyzing = false;

    // Filter untuk Chart Produktivitas
    public $timeFilter = 'month'; // 'week', 'month', 'year'

    // --- CHART DATA COMPUTED PROPERTIES ---

    public function getStatusChartDataProperty()
    {
        $userId = Auth::id();
        $pending = Task::where('user_id', $userId)->where('status', 'pending')->count();
        $completed = Task::where('user_id', $userId)->where('status', 'completed')->count();

        return [
            'labels' => ['Pending', 'Completed'],
            'data' => [$pending, $completed],
        ];
    }

    public function getPriorityChartDataProperty()
    {
        $userId = Auth::id();
        // Mengambil count berdasarkan priority pending saja (fokus summary)
        $high = Task::where('user_id', $userId)->where('status', 'pending')->where('priority', 'High')->count();
        $middle = Task::where('user_id', $userId)->where('status', 'pending')->where('priority', 'Middle')->count();
        $low = Task::where('user_id', $userId)->where('status', 'pending')->where('priority', 'Low')->count();

        return [
            'labels' => ['High', 'Middle', 'Low'],
            'data' => [$high, $middle, $low],
        ];
    }

    

    // --- ACTIONS ---

    public function setFilter($filter)
    {
        $this->timeFilter = $filter;
        // Dispatch event untuk update chart di frontend
        $this->dispatch('update-line-chart', data: $this->productivityTrend);
    }

    public function generateInsight(AIService $aiService)
    {
        $this->isAnalyzing = true;

        $tasks = Task::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->select('title', 'priority', 'due_date')
            ->get();

        $taskListString = $tasks->map(function ($t) {
            return "- {$t->title} ({$t->priority}, Due: " . ($t->due_date ? $t->due_date->format('Y-m-d') : 'None') . ")";
        })->implode("\n");

        if (empty($taskListString)) {
            $this->aiAnalysis = "Semua tugas selesai! Anda bebas hari ini.";
            $this->productivityTip = "Pertahankan momentum ini. Luangkan waktu untuk istirahat atau pelajari skill baru.";
        } else {
            // Prompt khusus untuk Summary + Tips
            // Asumsi method generateBriefing di AIService fleksibel menerima prompt custom
            // Atau kita buat string prompt di sini
            $prompt = "Berikut adalah daftar tugas saya:\n" . $taskListString . "\n\n" .
                "1. Berikan ringkasan singkat (maksimal 3 kalimat) tentang apa yang harus saya selesaikan segera berdasarkan prioritas dan deadline.\n" .
                "2. Berikan 1 tips produktivitas random yang unik dan actionable (tidak klise) untuk hari ini. Pisahkan output dengan delimiter '|||'.";

            // Kita anggap service mengembalikan raw text
            $response = $aiService->generateBriefing($prompt);

            // Parsing hasil (Simple split)
            $parts = explode('|||', $response);
            $this->aiAnalysis = trim($parts[0] ?? 'Analisa gagal.');
            $this->productivityTip = trim($parts[1] ?? 'Tetap fokus dan semangat!');
        }

        $this->isAnalyzing = false;
    }

    public function render()
    {
        return view('livewire.summary');
    }
}