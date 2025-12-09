<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class AdminDashboard extends Component
{
      use WithPagination;

    public $search = '';

    // Reset halaman pagination saat searching
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function getStatsProperty()
    {
        return [
            'total_users' => User::count(),
            'total_tasks' => Task::count(),
            'active_users' => User::has('tasks')->count(), // User yang punya minimal 1 task
            'vip_users' => User::where('role', 'admin')->count(),
        ];
    }

    public function deleteUser($userId)
    {
        // Jangan hapus diri sendiri
        if ($userId == auth()->id()) {
            session()->flash('error', 'Anda tidak bisa menghapus akun sendiri.');
            return;
        }

        $user = User::find($userId);
        if ($user) {
            // Hapus semua task user tersebut dulu
            $user->tasks()->delete();
            $user->delete();
            session()->flash('message', 'User berhasil dihapus.');
        }
    }

    public function toggleRole($userId)
    {
         if ($userId == auth()->id()) return;

         $user = User::find($userId);
         if($user){
             $user->role = $user->role === 'admin' ? 'user' : 'admin';
             $user->save();
         }
    }

    public function render()
    {
        $users = User::query()
            ->withCount(['tasks', 'tasks as pending_tasks_count' => function ($query) {
                $query->where('status', 'pending');
            }])
            ->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin-dashboard', [
            'users' => $users
        ]);
    }
}
