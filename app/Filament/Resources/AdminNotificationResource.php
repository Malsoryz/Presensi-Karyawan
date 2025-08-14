<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminNotificationResource\Pages;
use App\Filament\Resources\AdminNotificationResource\RelationManagers;
use App\Models\AdminNotification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Notifications\Notification as Notif;

use Filament\Forms\Components\TextInput;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;

use Filament\Tables\Actions\Action;

use Filament\Support\Enums\FontWeight;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Blade;

class AdminNotificationResource extends Resource
{
    protected static ?string $model = AdminNotification::class;

    protected static ?string $navigationLabel = 'Notification';
    protected static ?string $navigationIcon = 'heroicon-o-bell';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Unread notification')
            ->paginated(false)
            ->query(AdminNotification::query())
            ->columns([
                Split::make([
                    Stack::make([
                        TextColumn::make('title')
                            ->size(TextColumn\TextColumnSize::Large)
                            ->color('primary')
                            ->weight(FontWeight::Bold),
                        TextColumn::make('description')
                    ]),
                    TextColumn::make('type')
                        ->size(TextColumn\TextColumnSize::Large)
                        ->badge()
                        ->weight(FontWeight::Bold)
                        ->color('primary')
                        ->grow(false),
                ]),
            ])
            ->recordAction('read')
            ->actions([
                Action::make('read')
                    ->accessSelectedRecords()
                    ->extraAttributes([
                        'class' => 'hidden'
                    ])
                    ->modalHeading(fn(AdminNotification $record) => new HtmlString(self::readNotifModalHeading($record)))
                    ->modalContent(fn(AdminNotification $record) => new HtmlString(self::readNotifModalContent($record)))
                    ->modalSubmitActionLabel('Approve'),
                Action::make('Approve')
                    ->button()
                    ->action(function (AdminNotification $record) {
                        $record->user()->update([
                            'status_approved' => true,
                        ]);
                        $record->delete();
                    }),
                Action::make('Ignore')
                    ->button()
                    ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function readNotifModalHeading(AdminNotification $record)
    {
        return Blade::render(<<<'BLADE'
            <h3>Read: <span class="font-bold text-primary-600 dark:text-primary-400">{{ $record->title }}</span></h3>
        BLADE, ['record' => $record]);
    }

    public static function readNotifModalContent(AdminNotification $record)
    {
        return Blade::render(<<<'BLADE'
            <div>
                {{ $record->description }}
            </div>
        BLADE, ['record' => $record]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdminNotifications::route('/'),
        ];
    }
}
