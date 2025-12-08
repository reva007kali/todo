<?php

namespace App\Livewire;

use App\Models\Task;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class TaskTable extends Component
{

    use WithPagination;

    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // Reset halaman ke 1 saat user mengetik search
    public function updatedSearch()
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
        $tasks = Task::where('user_id', Auth::id())
            ->where('title', 'like', '%' . $this->search . '%') // Fitur Search
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10); // 10 data per halaman

        return view('livewire.task-table', [
            'tasks' => $tasks
        ]);
    }
}
