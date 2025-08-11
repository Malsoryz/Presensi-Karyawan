<?php

namespace App\Filament\Resources\MasterResource\Pages;

use App\Models\Jabatan;
use App\Models\Tunjangan;

use App\Filament\Resources\MasterResource;
use Filament\Actions;
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

class JabatanTable extends Page implements HasTable, HasForms
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string $resource = MasterResource::class;

    protected static string $view = 'filament.resources.master-resource.pages.jabatan';

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
                        ->id('nama-jabatan')
                        ->label('Nama Jabatan')
                        ->placeholder('i.e: Manajer'),
                    TextInput::make('gaji_pokok_bulanan')
                        ->label('Gaji Pokok Bulanan')
                        ->placeholder('i.e: 1.000.000')
                        ->required()
                        ->numeric()
                        ->prefix('Rp')
                        ->mask(RawJs::make(<<<'JS'
                            $money($input, ',', '.');
                        JS))
                        ->stripCharacters('.'),
                ]),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Jabatan::query())
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama Jabatan')
                    ->sortable(),
                TextColumn::make('id')
                    ->label('Jumlah Tunjangan')
                    ->alignCenter()
                    ->formatStateUsing(function ($state) {
                        $result = Tunjangan::whereHas('jabatan', function($query) use ($state) {
                            $query->where('jabatan_id', $state);
                        });
                        return count($result->get());
                    }),
                TextColumn::make('gaji_pokok_bulanan')
                    ->label('Gaji Pokok')
                    ->formatStateUsing(fn ($state) => 'Rp. ' . number_format($state, 0, ',', '.')),
            ])
            ->filters([
                //
            ])
            ->recordUrl(
                fn(Jabatan $record): string => route('filament.admin.resources.masters.tunjangan', ['record' => $record->id])
            )
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah')
                    ->modalHeading('Tambah Jabatan')
                    ->form($this->getFormSchema())
                    ->action(fn($data) => Jabatan::create($data)),
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
            'Jabatan'
        ];
    }
}
