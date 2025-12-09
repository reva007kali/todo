<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'due_date',
        'status',
        'priority',
        'is_reminder_sent'
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sharedWith()
    {
        return $this->belongsToMany(User::class, 'task_shares')
            ->withPivot('permission')
            ->withTimestamps();
    }

    // Helper untuk cek akses
    public function canEdit(User $user)
    {
        return $this->user_id === $user->id ||
            $this->sharedWith()->where('user_id', $user->id)->wherePivot('permission', 'edit')->exists();
    }
}
