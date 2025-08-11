<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\AdminNotification;

use Filament\Actions\Action;
use Filament\Notifications\Notification as Notif;
class Notification extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-bell';

    protected static string $view = 'filament.pages.notification';

    public function getNotifications()
    {
        return AdminNotification::all();
    }

    public function getNotificationAction(): object
    {
        return (object) [
            'approve' => (object) [
                'label' => 'Approve',
            ],
            'ignore' => (object) [
                'label' => 'Ignore'
            ],
        ];
    }

    public function responseApproveRequest($notif_id): void
    {
        $user = AdminNotification::find((int) $notif_id)->user;
        $user->update([
            'status_approved' => true,
        ]);
        Notif::make()
            ->title("Account {$user->name} now approved")
            ->success()
            ->send();
        AdminNotification::destroy($notif_id);
    }
}
