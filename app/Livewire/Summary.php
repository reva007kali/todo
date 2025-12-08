<?php

namespace App\Livewire;

use App\Models\Task;
use Livewire\Component;
use App\Services\AIService;
use Illuminate\Support\Facades\Auth;

class Summary extends Component
{
    public $userPrompt = '';
    public $aiSummary = null; // Menyimpan hasil summary AI
    public $isGeneratingSummary = false;

    // --- COMPUTED PROPERTIES UNTUK DASHBOARD ---

    // 1. Total Completed
    public function getCompletedCountProperty()
    {
        return Task::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->count();
    }

    // 2. High Priority Focus (3 Teratas: High Priority + Tanggal Terdekat)
    public function getImportantTasksProperty()
    {
        return Task::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->where('priority', 'High')
            ->orderBy('due_date', 'asc') // Yang deadline-nya paling dekat
            ->take(3)
            ->get();
    }

    // 3. Latest Activity (3 Tugas terakhir dibuat)
    public function getLatestTasksProperty()
    {
        return Task::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
    }

    // 4. Semua Pending (Untuk dikirim ke AI Summary & List Utama)
    public function getAllPendingTasksProperty()
    {
        return Task::where('user_id', Auth::id())
             ->orderByRaw("
                   CASE priority 
                       WHEN 'High' THEN 1 
                       WHEN 'Middle' THEN 2 
                       WHEN 'Low' THEN 3 
                       ELSE 4 
                   END
               ")
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // --- ACTIONS ---

    public function generateDailyBriefing(AIService $aiService)
    {
        $this->isGeneratingSummary = true;
        
        // Ambil judul tugas pending saja untuk hemat token
        $tasksList = $this->allPendingTasks->pluck('title')->implode(", ");

        if (empty($tasksList)) {
            $this->aiSummary = "Tidak ada tugas pending. Kerja bagus!";
        } else {
            $this->aiSummary = $aiService->generateBriefing($tasksList);
        }

        $this->isGeneratingSummary = false;
    }

    public function processPrompt(AIService $aiService)
    {
        $this->validate(['userPrompt' => 'required|string|min:3']);
        
        try {
            $jsonResponse = $aiService->parseTask($this->userPrompt);
            $data = json_decode($jsonResponse, true);

            $priority = in_array($data['priority'] ?? '', ['Low', 'Middle', 'High']) ? $data['priority'] : 'Low';

            Task::create([
                'user_id' => Auth::id(),
                'title' => $data['title'] ?? 'New Task',
                'description' => $data['description'] ?? $this->userPrompt,
                'priority' => $priority,
                'due_date' => $data['due_date'],
            ]);

            $this->userPrompt = '';
            session()->flash('message', 'Task created successfully.');

        } catch (\Exception $e) {
            session()->flash('error', 'AI Error: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        $task = Task::find($id);
        if ($task && $task->user_id == Auth::id()) {
            $task->status = $task->status === 'pending' ? 'completed' : 'pending';
            $task->save();
        }
    }

    // Helper Styles
    public function getPriorityColor($priority)
    {
        return match($priority) {
            'High' => 'bg-red-50 text-red-700 border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20',
            'Middle' => 'bg-yellow-50 text-yellow-700 border-yellow-200 dark:bg-yellow-500/10 dark:text-yellow-400 dark:border-yellow-500/20',
            'Low' => 'bg-zinc-100 text-zinc-700 border-zinc-200 dark:bg-zinc-500/10 dark:text-zinc-400 dark:border-zinc-500/20',
        };
    }

    public function render()
    {
        return view('livewire.summary');
    }
}
