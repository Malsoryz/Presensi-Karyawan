<?php

namespace App\Livewire\Tables\User;

use App\Models\Jabatan as Jbt;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;
use Illuminate\Contracts\View\View;

class Jabatan extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Jbt::query())
            ->columns([
                Tables\Columns\TextColumn::make('nama')->label('Nama Jabatan')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('gaji_pokok_bulanan')->label('Gaji Pokok')->formatStateUsing(fn ($state) => 'Rp. ' . number_format($state, 0, ',', '.')),
                Tables\Columns\TextColumn::make('tunjangan_kehadiran_harian')->label('Tunjangan Kehadiran')->formatStateUsing(fn ($state) => 'Rp. ' . number_format($state, 0, ',', '.')),
            ])
            ->headerActions([
                Tables\Actions\Action::make('create')
                    ->label('Tambah Jabatan')
                    ->button()
                    ->modalHeading('Tambah Jabatan')
                    ->form([
                        TextInput::make('nama')->required()->label('Nama Jabatan'),
                        TextInput::make('gaji_pokok_bulanan')->numeric()->required()->label('Gaji Pokok Bulanan'),
                        TextInput::make('tunjangan_kehadiran_harian')->numeric()->required()->label('Tunjangan Kehadiran Harian'),
                    ])
                    ->action(function (array $data) {
                        Jbt::create($data);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading('Edit Jabatan')
                    ->form([
                        TextInput::make('nama')->required()->label('Nama Jabatan'),
                        TextInput::make('gaji_pokok_bulanan')->numeric()->required()->label('Gaji Pokok Bulanan'),
                        TextInput::make('tunjangan_kehadiran_harian')->numeric()->required()->label('Tunjangan Kehadiran Harian'),
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
        return view('livewire.tables.user.jabatan');
    }
}