<?php

namespace App\Livewire\Tables\Presensi;

use App\Models\Quote;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

use Filament\Tables\Columns\TextColumn;

use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

use Illuminate\Database\Eloquent\Collection;

class QuotesTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function getFormSchema(): array
    {
        return [
            TextInput::make('author')
                ->label('Motivator'),
            Textarea::make('motivation')
                ->label('Kata-Kata'),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Quote')
            ->description('Kumpulan kata-kata yang akan di tampilkan di halaman presensi')
            ->query(Quote::query())
            ->columns([
                TextColumn::make('motivation')
                    ->label('Kata-Kata'),
                TextColumn::make('author')
                    ->label('Motivator'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Quote')
                    ->modalHeading('Tambah Quote Baru')
                    ->form([...$this->getFormSchema()])
            ])
            ->actions([
                EditAction::make('edit-quote')
                    ->modalHeading(function (Quote $record) {
                        return "Edit Quote: {$record->author}";
                    })
                    ->form([...$this->getFormSchema()]),
                DeleteAction::make(),
            ])
            ->recordAction('edit-quote')
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
