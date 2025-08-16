<?php

namespace App\Livewire\Tables\Presensi;

use App\Models\PesanStatus;
use App\Enums\Presensi\StatusPresensi;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\View as FormView;

class StatusTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public array $tabItem = [];

    public string $activeTab = 'masuk';

    public function mount()
    {
        $itemFromEnum = collect(StatusPresensi::itemForStatusMessage())->flip();
        $this->tabItem = [...$itemFromEnum];
    }

    public function setTab(string $tabId)
    {
        $this->activeTab = $tabId;
        $this->resetTable();
    }

    public function getTableQuery(): Builder|Relation
    {
        return match ($this->activeTab) {
            'masuk' => PesanStatus::queryMasuk(),
            'terlambat' => PesanStatus::queryTerlambat(),
            default => PesanStatus::query(),
        };
    }

    public function createActionFormSchema(): array
    {
        return [
            Select::make('type')
                ->required()
                ->options(StatusPresensi::itemForStatusMessage()),
            RichEditor::make('template')
                ->required()
                ->label('Template'),
            FormView::make('filament.components.view.text')
                ->viewData([
                    'heading' => 'Catatan',
                ]),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->header(view('filament.components.override.table.header', [
                'header' => 'Pesan Status Presensi',
                'description' => 'Pesan yang akan ditampilkan di halaman presensi'
            ]))
            ->columns([
                TextColumn::make('template'),
                // TextColumn::make('type'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Template')
                    ->modalHeading('Tambah template pesan status')
                    ->form([...$this->createActionFormSchema()]),
            ])
            ->actions([
                EditAction::make('edit_template')
                    ->modalHeading('Edit Template')
                    ->form([...$this->createActionFormSchema()]),
                DeleteAction::make(),
            ])
            ->recordAction('edit_template')
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
