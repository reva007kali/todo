<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Task;
use App\Models\User;
use Livewire\Component;
use App\Services\AIService;
use Illuminate\Support\Facades\Auth;

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

    // State untuk Sharing
    public $shareModalOpen = false;
    public $taskToShareId = null;
    public $shareEmail = '';
    public $sharePermission = 'edit';

    // Filter Tampilan (My Tasks / Shared With Me)
    public $viewFilter = 'my_tasks'; // 'my_tasks' atau 'shared'

    // Computed Property untuk Tasks
    public function getTasksProperty()
    {
        if ($this->viewFilter === 'shared') {
            // Ambil task yang dishare KE saya
            return Auth::user()->sharedTasks()
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // Ambil task milik saya sendiri (Logic lama)
        return Task::where('user_id', Auth::id())
            ->orderByRaw("CASE WHEN status = 'completed' THEN 1 ELSE 0 END")
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

    // --- LOGIC SHARING ---

    public function openShareModal($taskId)
    {
        $this->taskToShareId = $taskId;
        $this->shareModalOpen = true;
        $this->shareEmail = '';
    }

    public function shareTask()
    {
        $this->validate([
            'shareEmail' => 'required|email|exists:users,email'
        ]);

        $task = Task::find($this->taskToShareId);
        $userToShare = User::where('email', $this->shareEmail)->first();

        // Validasi: Jangan share ke diri sendiri & Cek kepemilikan
        if ($userToShare->id === Auth::id()) {
            $this->addError('shareEmail', 'Anda tidak bisa share ke diri sendiri.');
            return;
        }

        if ($task->user_id !== Auth::id()) {
            session()->flash('error', 'Hanya pemilik yang bisa membagikan task.');
            return;
        }

        // Action Share
        try {
            $task->sharedWith()->syncWithoutDetaching([
                $userToShare->id => ['permission' => $this->sharePermission]
            ]);

            $this->shareModalOpen = false;
            session()->flash('message', "Task berhasil dibagikan ke {$userToShare->name}");
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal membagikan task.');
        }
    }

    public function removeShare($userId)
    {
        $task = Task::find($this->taskToShareId);
        if ($task->user_id === Auth::id()) {
            $task->sharedWith()->detach($userId);
            // Refresh modal data logic if needed, or just let re-render happen
        }
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

        // Cek apakah pemilik ATAU punya akses edit
        if ($task && ($task->user_id == Auth::id() || $task->canEdit(Auth::user()))) {
            $task->update([
                'title' => $this->editForm['title'],
                'description' => $this->editForm['description'],
                'priority' => $this->editForm['priority'],
                'due_date' => $this->editForm['due_date'] ?: null,
            ]);

            $this->expandedTaskId = null;
            session()->flash('message', 'Perubahan berhasil disimpan.');
        } else {
            session()->flash('error', 'Anda tidak memiliki izin edit.');
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

        // LOGIC BARU:
        // Cek apakah User adalah PEMILIK -ATAU- Punya akses EDIT via Share
        if ($task && ($task->user_id == Auth::id() || $task->canEdit(Auth::user()))) {

            $task->status = $task->status === 'pending' ? 'completed' : 'pending';
            $task->save();

        } else {
            // Opsional: Beri notifikasi jika user 'View Only' mencoba klik
            session()->flash('error', 'Anda hanya memiliki akses lihat (View Only).');
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