<?php

namespace App\Livewire\Layout;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;

class Sidebar extends Component
{
    // Menggunakan Computed Property agar efisien (dicache per request)
    // #[On('task-updated')] artinya: Jika ada component lain dispatch event 'task-updated', 
    // fungsi ini akan dihitung ulang otomatis.
    #[Computed]
    #[On('task-updated')]
    #[On('echo:private-user.{userId},TaskShared')] // Opsional: Jika nanti pakai Pusher/Reverb realtime
    public function sharedCount()
    {
        if (!Auth::check()) {
            return 0;
        }

        return Auth::user()
            ->sharedTasks()
            ->where('status', 'pending')
            ->count();
    }

    // Properti untuk userId (jika butuh untuk broadcasting channel)
    public function getUserIdProperty()
    {
        return Auth::id();
    }

    public function render()
    {
        return view('livewire.layout.sidebar');
    }
}
