<?php

namespace App\Livewire;

use App\Models\Task;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class TaskTable extends Component
{
    use WithPagination;

    // Filter State
    public $search = '';
    public $filterStatus = 'all';   // 'all', 'pending', 'completed'
    public $filterPriority = 'all'; // 'all', 'High', 'Middle', 'Low'
    
    // Sorting State
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // Reset pagination saat filter berubah
    public function updatedSearch() { $this->resetPage(); }
    public function updatedFilterStatus() { $this->resetPage(); }
    public function updatedFilterPriority() { $this->resetPage(); }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
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

    public function deleteTask($id)
    {
        $task = Task::find($id);
        if ($task && $task->user_id == Auth::id()) {
            $task->delete();
            session()->flash('message', 'Tugas berhasil dihapus.');
        }
    }

    // Helper warna badge (sama dengan Dashboard)
    public function getPriorityClasses($priority)
    {
        return match ($priority) {
            'High' => 'bg-red-50 text-red-700 border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20',
            'Middle' => 'bg-yellow-50 text-yellow-700 border-yellow-200 dark:bg-yellow-500/10 dark:text-yellow-400 dark:border-yellow-500/20',
            'Low' => 'bg-zinc-100 text-zinc-700 border-zinc-200 dark:bg-zinc-500/10 dark:text-zinc-400 dark:border-zinc-500/20',
            default => 'bg-zinc-100 text-zinc-700 border-zinc-200 dark:bg-zinc-500/10 dark:text-zinc-400 dark:border-zinc-500/20',
        };
    }

    public function render()
    {
        $query = Task::where('user_id', Auth::id());

        // 1. Search Logic (Title OR Description)
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // 2. Filter Status
        $query->when($this->filterStatus !== 'all', function ($q) {
            $q->where('status', $this->filterStatus);
        });

        // 3. Filter Priority
        $query->when($this->filterPriority !== 'all', function ($q) {
            $q->where('priority', $this->filterPriority);
        });

        // 4. Sorting & Pagination
        $tasks = $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.task-table', [
            'tasks' => $tasks
        ]);
    }
}