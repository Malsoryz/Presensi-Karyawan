<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AdminNotification;
use App\Enums\Notification\Type;

class NotifExample extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AdminNotification::create([
            'title' => 'Approval Request',
            'description' => "New Approval request for new account 'user'",
            'type' => 'approval',
            'sender_id' => 3,
        ]);
    }
}
