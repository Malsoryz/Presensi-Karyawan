<?php

namespace App\Filament\Resources\MasterResource\Pages;

use App\Models\Divisi;
use App\Filament\Resources\MasterResource;
use Filament\Resources\Pages\Page;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\CreateAction;

use Filament\Forms\Form;
use Filament\Support\RawJs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Grid;

use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class DivisiTable extends Page implements HasTable, HasForms
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string $resource = MasterResource::class;

    protected static string $view = 'filament.resources.master-resource.pages.divisi';

    protected static ?string $title = 'Master';

    public function getFormSchema(): array
    {
        return [
            Grid::make([
                    'default' => 1,
                ])
                ->schema([
                    TextInput::make('nama')
                        ->required()
                        ->label('Nama Divisi')
                        ->placeholder('i.e: Programmer'),
                ]),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Divisi::query())
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama Divisi')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah')
                    ->modalHeading('Tambah Divisi')
                    ->form($this->getFormSchema())
                    ->action(fn($data) => Divisi::create($data)),
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
            'Divisi',
        ];
    }
}
