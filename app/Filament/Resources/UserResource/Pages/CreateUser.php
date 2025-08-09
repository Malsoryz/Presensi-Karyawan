<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

use Filament\Support\Enums\MaxWidth;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected static string $view = 'filament.resources.user-resource.pages.create-user';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status_approved'] = true;
        return $data;
    }
}
