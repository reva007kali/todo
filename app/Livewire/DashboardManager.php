<?php

namespace App\Livewire;

use App\Models\Task;
use Livewire\Component;
use App\Services\AIService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardManager extends Component
{
    // AI Input
    public $userPrompt = '';
    public $isLoading = false;
    public $confirmingDeleteId = null;


    // Manual Creation State
    public $isCreating = false;
    public $createForm = [
        'title' => '',
        'description' => '',
        'priority' => 'Middle',
        'due_date' => '',
    ];

    // Editing State (Accordion)
    public $expandedTaskId = null;
    public $editForm = [
        'title' => '',
        'description' => '',
        'priority' => '',
        'due_date' => '',
    ];

    // Computed Property untuk Tasks
    public function getTasksProperty()
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

    // Computed Property untuk Stats
    public function getStatsProperty()
    {
        $tasks = $this->tasks; // Menggunakan cache dari property di atas jika ada, atau query baru
        return [
            'total' => $tasks->count(),
            'pending' => $tasks->where('status', 'pending')->count(),
            'completed' => $tasks->where('status', 'completed')->count(),
        ];
    }

    // --- AI Logic ---
    public function processPrompt(AIService $aiService)
    {
        $this->validate(['userPrompt' => 'required|string|min:3']);
        $this->isLoading = true;

        try {
            $jsonResponse = $aiService->parseTask($this->userPrompt);
            $data = json_decode($jsonResponse, true);

            $priority = in_array($data['priority'] ?? '', ['Low', 'Middle', 'High']) ? $data['priority'] : 'Low';

            // Format tanggal agar sesuai input datetime-local HTML
            $dueDate = $data['due_date'] ? Carbon::parse($data['due_date'])->format('Y-m-d\TH:i') : null;

            Task::create([
                'user_id' => Auth::id(),
                'title' => $data['title'] ?? 'Tugas Baru',
                'description' => $data['description'] ?? $this->userPrompt,
                'priority' => $priority,
                'due_date' => $dueDate,
            ]);

            $this->userPrompt = '';
            session()->flash('message', 'Tugas AI berhasil dibuat!');
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    // --- Manual Creation Logic ---
    public function toggleCreate()
    {
        $this->isCreating = !$this->isCreating;
        // Reset form defaults
        $this->createForm = [
            'title' => '',
            'description' => '',
            'priority' => 'Middle',
            'due_date' => now()->format('Y-m-d\TH:i'), // Default to now
        ];
    }

    public function saveManualTask()
    {
        $this->validate([
            'createForm.title' => 'required|min:3',
        ]);

        Task::create([
            'user_id' => Auth::id(),
            'title' => $this->createForm['title'],
            'description' => $this->createForm['description'],
            'priority' => $this->createForm['priority'],
            'due_date' => $this->createForm['due_date'] ?: null,
        ]);

        $this->isCreating = false;
        session()->flash('message', 'Tugas manual berhasil dibuat.');
    }

    // --- Accordion / Edit Logic ---
    public function toggleExpand($taskId)
    {
        // Jika klik yang sama, tutup. Jika beda, buka yang baru.
        if ($this->expandedTaskId === $taskId) {
            $this->expandedTaskId = null;
            $this->editForm = [];
        } else {
            $this->expandedTaskId = $taskId;
            $task = Task::find($taskId);

            // Isi form edit
            $this->editForm = [
                'title' => $task->title,
                'description' => $task->description,
                'priority' => $task->priority,
                // Format tanggal untuk input HTML
                'due_date' => $task->due_date ? $task->due_date->format('Y-m-d\TH:i') : '',
            ];
        }
    }

    public function updateTask()
    {
        $task = Task::find($this->expandedTaskId);

        if ($task && $task->user_id == Auth::id()) {
            $task->update([
                'title' => $this->editForm['title'],
                'description' => $this->editForm['description'],
                'priority' => $this->editForm['priority'],
                'due_date' => $this->editForm['due_date'] ?: null,
            ]);

            $this->expandedTaskId = null; // Tutup accordion
            session()->flash('message', 'Perubahan berhasil disimpan.');
        }
    }

     public function deleteTask()
    {
        if ($this->confirmingDeleteId) {
            $task = Task::find($this->confirmingDeleteId);
            
            if ($task && $task->user_id == Auth::id()) {
                $task->delete();
                
                // Jika task yang dihapus sedang dalam mode edit (expanded), tutup accordion-nya
                if ($this->expandedTaskId == $this->confirmingDeleteId) {
                    $this->expandedTaskId = null;
                }

                session()->flash('message', 'Tugas berhasil dihapus.');
            }
        }

        // Reset state
        $this->confirmingDeleteId = null;
    }




    public function toggleStatus($taskId)
    {
        $task = Task::find($taskId);
        if ($task && $task->user_id == Auth::id()) {
            $task->status = $task->status === 'pending' ? 'completed' : 'pending';
            $task->save();
        }
    }

    public function confirmDelete($taskId)
    {
        $this->confirmingDeleteId = $taskId;
    }
    public function cancelDelete()
    {
        $this->confirmingDeleteId = null;
    }

    public function getPriorityColor($priority)
    {
        return match ($priority) {
            'High' => 'text-red-700 bg-red-50 ring-red-600/20 dark:bg-red-900/30 dark:text-red-400',
            'Middle' => 'text-yellow-700 bg-yellow-50 ring-yellow-600/20 dark:bg-yellow-900/30 dark:text-yellow-400',
            'Low' => 'text-blue-700 bg-blue-50 ring-blue-600/20 dark:bg-blue-900/30 dark:text-blue-400',
            default => 'text-zinc-600 bg-zinc-50 ring-zinc-500/10 dark:bg-zinc-800 dark:text-zinc-400',
        };
    }

    public function render()
    {
        return view('livewire.dashboard-manager');
    }
}