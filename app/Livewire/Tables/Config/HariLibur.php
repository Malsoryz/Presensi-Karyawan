<?php

namespace App\Livewire\Tables\Config;

use App\Models\HariLibur as HL;

use App\Enums\Month;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Artisan;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;

use Filament\Notifications\Notification;

use Illuminate\Support\HtmlString;
use Carbon\Carbon;

use Livewire\Component;

class HariLibur extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        $defaultMonth = request()->get('month') ?? 1;
        $allMonthOptions = [];
        for ($i = 1; $i <= 12; $i++) { 
            $allMonthOptions[(string)$i] = Carbon::createFromDate(null, $i, null)->translatedFormat('F');
        }

        return $table
            ->paginated(false)
            ->query(HL::query()->orderBy('tanggal'))
            ->columns([
                TextColumn::make('bulan')
                    ->label('Bulan')
                    ->formatStateUsing(fn (string $state) => Carbon::createFromDate(null, $state, null)->translatedFormat('F')),
                TextColumn::make('nama')
                    ->label('Nama Hari Libur'),
                TextColumn::make('tanggal')
                    ->label('Tanggal')
            ])
            ->filters([
                SelectFilter::make('bulan')
                    ->default($defaultMonth)
                    ->options($allMonthOptions)
            ])
            ->emptyStateHeading(function ($livewire) {
                $state = $livewire->getTableFilterState('bulan')['value'];
                $bulan = empty($state) ? $state : Month::tryFrom($state);
                return $bulan === null || empty($bulan) ? 'There are no holidays' : 'There are no holidays in '.$bulan->name;
            })
            ->emptyStateDescription(!HL::exists() ? 'No holidays at all, try reloading.' : null)
            ->emptyStateActions(!HL::exists() ? [
                Action::make('reload')
                    ->label('Reload')
                    ->action(function () {
                        Artisan::call('holiday:update');
                        Notification::make()
                            ->title('Update Success!')
                            ->body('Successfully update holiday list')
                            ->success()
                            ->send();
                    }),
            ] : [])
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
