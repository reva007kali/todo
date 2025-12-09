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
    public $filterStatus = 'all';
    public $filterPriority = 'all';

    // Sorting State
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // Modal State
    public $selectedTask = null;
    public $isViewModalOpen = false;

    // Reset pagination saat filter berubah
    public function updatedSearch()
    {
        $this->resetPage();
    }
    public function updatedFilterStatus()
    {
        $this->resetPage();
    }
    public function updatedFilterPriority()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    // -- Modal Actions --
    public function openViewModal($id)
    {
        $this->selectedTask = Task::find($id);
        if ($this->selectedTask && $this->selectedTask->user_id == Auth::id()) {
            $this->isViewModalOpen = true;
        }
    }

    public function closeViewModal()
    {
        $this->isViewModalOpen = false;
        $this->selectedTask = null;
    }
    // -------------------

    public function toggleStatus($id)
    {
        $task = Task::find($id);
        if ($task && $task->user_id == Auth::id()) {
            $task->status = $task->status === 'pending' ? 'completed' : 'pending';
            $task->save();

            // Jika sedang melihat modal task ini, update datanya juga
            if ($this->selectedTask && $this->selectedTask->id == $id) {
                $this->selectedTask->refresh();
            }
        }
    }

    public function deleteTask($id)
    {
        $task = Task::find($id);
        if ($task && $task->user_id == Auth::id()) {
            $task->delete();
            $this->closeViewModal(); // Tutup modal jika task yang dibuka dihapus
            session()->flash('message', 'Tugas berhasil dihapus.');
        }
    }

    // Helper warna badge
    public function getPriorityClasses($priority)
    {
        return match ($priority) {
            'High' => 'bg-red-50 text-red-700 border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800',
            'Middle' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/30 dark:text-amber-400 dark:border-amber-800',
            'Low' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800',
            default => 'bg-zinc-100 text-zinc-700 border-zinc-200 dark:bg-zinc-800 dark:text-zinc-400 dark:border-zinc-700',
        };
    }

    public function render()
    {
        $query = Task::where('user_id', Auth::id());

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        $query->when($this->filterStatus !== 'all', fn($q) => $q->where('status', $this->filterStatus));
        $query->when($this->filterPriority !== 'all', fn($q) => $q->where('priority', $this->filterPriority));

        $tasks = $query->orderBy($this->sortField, $this->sortDirection)->paginate(10);

        return view('livewire.task-table', [
            'tasks' => $tasks
        ]);
    }
}