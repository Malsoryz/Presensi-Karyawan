<?php

namespace App\Filament\Resources\MasterResource\Pages;

use App\Models\Tunjangan;
use App\Models\Jabatan;
use App\Filament\Resources\MasterResource;
use Filament\Actions;
use Filament\Resources\Pages\Page;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\CreateAction;

use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Grid;
use Filament\Support\RawJs;

use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class TunjanganEdit extends Page implements HasTable, HasForms
{
    use InteractsWithForms;
    use InteractsWithTable;
    use InteractsWithRecord;

    protected static string $resource = MasterResource::class;

    protected static string $view = 'filament.resources.master-resource.pages.tunjangan';

    public function getTitle(): Htmlable|string
    {
        return "Edit tunjangan: {$this->getRecord()->nama}";
    }

    public function resolveRecord(int|string $key): Jabatan
    {
        return Jabatan::findOrFail((int) $key);
    }

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    public function getFormSchema(): array
    {
        return [
            Grid::make(1)
                ->schema([
                    TextInput::make('nama')
                        ->required()
                        ->label('Nama Tunjangan')
                        ->placeholder('i.e: Hari raya'),
                    TextInput::make('jumlah')
                        ->label('Jumlah tunjangan')
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
            ->query(Tunjangan::query()->where('jabatan_id', $this->getRecord()->id))
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama Tunjangan')
                    ->sortable(),
                TextColumn::make('jumlah')
                    ->label('Jumlah tunjangan')
                    ->formatStateUsing(fn ($state) => 'Rp. ' . number_format($state, 0, ',', '.')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah')
                    ->modalHeading('Tambah Tunjangan')
                    ->form($this->getFormSchema())
                    ->action(function ($data) {
                        $this->getRecord()->tunjangan()->create($data);
                    }),
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

    public function getBreadcrumbs(): array
    {
        return [
            'Master',
            route('filament.admin.resources.masters.index') => 'Jabatan',
            'Tunjangan'
        ];
    }
}
