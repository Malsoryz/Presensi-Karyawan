<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class AdminNotification extends Model
{
    protected $fillable = [
        'title',
        'description',
        'type',
        'sender_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
