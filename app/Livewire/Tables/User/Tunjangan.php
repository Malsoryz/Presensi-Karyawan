<?php

namespace App\Livewire\Tables\User;

use App\Models\Tunjangan as Tunjang;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\TextColumn;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

use Livewire\Component;
use Closure;

class Tunjangan extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public Closure|int|null $id;

    public function table(Table $table): Table
    {
        return $table
            ->query(Tunjang::query()->where('jabatan_id', $this->id))
            ->paginated(false)
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
            ->actions([
                //
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //
                ]),
            ]);
    }

    public function render()
    {
        return <<<'BLADE'
            <div>
                {{ $this->table }}
            </div>
        BLADE;
    }
}
