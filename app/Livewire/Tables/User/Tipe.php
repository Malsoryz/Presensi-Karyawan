<?php

namespace App\Livewire\Tables\User;

use App\Models\Tipe as Tp;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;
use Illuminate\Contracts\View\View;

class Tipe extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Tp::query())
            ->columns([
                Tables\Columns\TextColumn::make('nama_tipe')->label('Nama Tipe')->searchable()->sortable(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('create')
                    ->label('Tambah Tipe')
                    ->button()
                    ->modalHeading('Tambah Tipe')
                    ->form([
                        TextInput::make('nama_tipe')->required()->label('Nama Tipe'),
                    ])
                    ->action(function (array $data) {
                        Tp::create($data);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading('Edit Tipe')
                    ->form([
                        TextInput::make('nama_tipe')->required()->label('Nama Tipe'),
                    ]),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.tables.user.tipe');
    }
}