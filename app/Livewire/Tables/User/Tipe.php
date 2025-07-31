<?php

namespace App\Livewire\Tables\User;

use App\Models\Tipe as Tp;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;
use Illuminate\Contracts\View\View;

use Filament\Support\Enums\Alignment;

use Filament\Tables\Columns\TextColumn;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Checkbox;

class Tipe extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Tp::query())
            ->columns([
                TextColumn::make('nama_tipe')
                    ->label('Nama Tipe'),
                TextColumn::make('wajib_upload')
                    ->label('Wajib upload file')
                    ->alignment(Alignment::Center)
                    ->formatStateUsing(fn (bool $state): string => $state ? 'yes' : 'no' )
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->model(Tp::class)
                    ->label('Add new Type')
                    ->modalHeading('Tambah Tipe')
                    ->form([
                        TextInput::make('nama_tipe')
                            ->required()
                            ->label('Nama Tipe'),
                        Checkbox::make('wajib_upload')
                            ->label('Wajib upload file'),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading(fn (Tp $record) => 'Edit ' . $record->nama_tipe)
                    ->form([
                        TextInput::make('nama_tipe')
                            ->required()
                            ->label('Nama Tipe'),
                        Checkbox::make('wajib_upload')
                            ->label('Wajib upload file'),
                    ]),
                // Tables\Actions\DeleteAction::make(),
            ]);
            // ->bulkActions([
            //     Tables\Actions\BulkActionGroup::make([
            //         Tables\Actions\DeleteBulkAction::make(),
            //     ]),
            // ]);
    }

    public function render(): View
    {
        return view('livewire.tables.user.tipe');
    }
}