<?php

namespace App\Filament\Resources\PresensiResource\Pages;

use App\Filament\Resources\PresensiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPresensis extends ListRecords
{
    protected static string $resource = PresensiResource::class;

    protected static ?string $title = 'Data Presensi';

    public function getBreadcrumbs(): array
    {
        return [
            route('filament.admin.resources.presensis.index') => 'Presensi',
            'List',
        ];
    }
}
