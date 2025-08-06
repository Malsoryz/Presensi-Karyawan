<?php

namespace App\Filament\Resources\MasterResource\Pages;

use App\Models\Tipe;
use App\Filament\Resources\MasterResource;
use Filament\Resources\Pages\Page;

use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\CreateAction;

use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Grid;

use Filament\Support\Enums\Alignment;

class TipeTable extends Page implements HasTable, HasForms
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string $resource = MasterResource::class;

    protected static string $view = 'filament.resources.master-resource.pages.tipe';

    protected static ?string $title = 'Master';

    public function getFormSchema(): array
    {
        return [
            Grid::make([
                    'default' => 1,
                ])
                ->schema([
                    TextInput::make('nama_tipe')
                        ->required()
                        ->label('Nama Tipe'),
                    Checkbox::make('wajib_upload')
                        ->label('Wajib upload file')
                ]),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Tipe::query())
            ->columns([
                TextColumn::make('nama_tipe')->label('Nama tipe')
                    ->sortable(),
                TextColumn::make('wajib_upload')
                    ->label('Wajib upload file')
                    ->alignment(Alignment::Center)
                    ->formatStateUsing(fn (bool $state): string => $state ? 'yes' : 'no' ),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah')
                    ->modalHeading('Tambah Tipe')
                    ->form($this->getFormSchema())
                    ->action(fn($data) => Tipe::create($data)),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form($this->getFormSchema()),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function getPageTabs(): array
    {
        return MasterResource::getPageTabs();
    }

    public function getBreadcrumbs(): array
    {
        return [
            'Master',
            'Tipe'
        ];
    }
}
