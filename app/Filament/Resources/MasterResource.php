<?php

namespace App\Filament\Resources;

use App\Models\User;
use App\Models\Jabatan;
use App\Filament\Resources\MasterResource\Pages;
use App\Filament\Resources\MasterResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MasterResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $title = 'Master';

    protected static ?string $navigationGroup = 'Users';

    protected static ?string $navigationLabel = 'Master';

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPageTabs(): array
    {
        return [
            [
                'label' => 'Jabatan',
                'route' => route('filament.admin.resources.masters.index'),
            ],
            [
                'label' => 'Tipe',
                'route' => route('filament.admin.resources.masters.tipe'),
            ],
            [
                'label' => 'Divisi',
                'route' => route('filament.admin.resources.masters.divisi'),
            ]
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\JabatanTable::route('/'),
            'tunjangan' => Pages\TunjanganEdit::route('/{record}/tunjangan'),
            'tipe' => Pages\TipeTable::route('/tipe'),
            'divisi' => Pages\DivisiTable::route('/divisi'),
        ];
    }
}
