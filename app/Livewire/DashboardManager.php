<?php

namespace App\Livewire;

use App\Models\Task;
use Livewire\Component;
use App\Services\AIService;
use Illuminate\Support\Facades\Auth;

class DashboardManager extends Component
{
    public $userPrompt = '';
    public $isLoading = false;
    public ?Task $selectedTask = null; // Menyimpan task yang sedang dibuka


    public function getTasksProperty()
    {
        return Task::where('user_id', Auth::id())
            // Menggunakan CASE WHEN agar kompatibel dengan SQLite & MySQL
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

    public function processPrompt(AIService $aiService)
    {
        $this->validate(['userPrompt' => 'required|string|min:3']);

        $this->isLoading = true;

        try {
            // 1. Kirim ke AI
            $jsonResponse = $aiService->parseTask($this->userPrompt);
            $data = json_decode($jsonResponse, true);

            // Validasi fallback jika AI mengembalikan nilai aneh untuk priority
            $validPriorities = ['Low', 'Middle', 'High'];
            $priority = in_array($data['priority'] ?? '', $validPriorities)
                ? $data['priority']
                : 'Low';

            // 2. Simpan ke Database
            Task::create([
                'user_id' => Auth::id(),
                'title' => $data['title'] ?? 'Tugas Baru',
                'description' => $data['description'] ?? $this->userPrompt,
                'priority' => $priority, // <--- Simpan Priority
                'due_date' => $data['due_date'],
            ]);

            $this->userPrompt = '';
            session()->flash('message', 'Tugas berhasil dibuat dengan prioritas: ' . $priority);

        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    public function toggleStatus($taskId)
    {
        $task = Task::find($taskId);
        if ($task && $task->user_id == Auth::id()) {
            $task->status = $task->status === 'pending' ? 'completed' : 'pending';
            $task->save();
        }
    }

    // Fungsi helper untuk warna badge di View (Opsional, biar rapi)
    // Tambahkan fungsi ini di dalam class DashboardManager
    public function getPriorityClasses($priority)
    {
        return match ($priority) {
            'High' => 'bg-red-50 text-red-700 border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20',
            'Middle' => 'bg-yellow-50 text-yellow-700 border-yellow-200 dark:bg-yellow-500/10 dark:text-yellow-400 dark:border-yellow-500/20',
            'Low' => 'bg-zinc-100 text-zinc-700 border-zinc-200 dark:bg-zinc-500/10 dark:text-zinc-400 dark:border-zinc-500/20',
            default => 'bg-zinc-100 text-zinc-700 border-zinc-200 dark:bg-zinc-500/10 dark:text-zinc-400 dark:border-zinc-500/20',
        };
    }

    public function selectTask(Task $task)
    {
        $this->selectedTask = $task;
    }

    public function closeTask()
    {
        $this->selectedTask = null;
    }

    public function deleteTask()
    {
        if ($this->selectedTask) {
            $this->selectedTask->delete();
            $this->selectedTask = null;
            session()->flash('message', 'Tugas berhasil dihapus.');
        }
    }

    public function render()
    {
        return view('livewire.dashboard-manager');
    }
}
