<?php

namespace App\Livewire\Tables\Config;

use App\Models\HariKerja as HK;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Artisan;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;

use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;
use Carbon\Carbon;

class HariKerja extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->query(HK::query())
            ->recordUrl(
                fn (HK $record): string => route('filament.admin.pages.settings', [
                    'tab' => '-hari-libur-tab',
                    'month' => $record->bulan,
                ])
            )
            ->columns([
                TextColumn::make('bulan')
                    ->label('Bulan')
                    ->formatStateUsing(fn (string $state): HtmlString => new HtmlString(Carbon::createFromDate(null, $state, null)->translatedFormat('F'))),
                TextColumn::make('total_hari')
                    ->label('Total Hari'),
                TextColumn::make('total_hari_minggu')
                    ->label('Total Hari Minggu'),
                TextColumn::make('total_hari_libur_nasional')
                    ->label('Total Hari Libur Nasional'),
                TextColumn::make('total_hari_libur')
                    ->label('Total Hari Libur'),
                TextColumn::make('total_hari_kerja')
                    ->label('Total Hari Kerja'),
            ])
            ->emptyStateHeading('No working days are known.')
            ->emptyStateDescription(!HK::exists() ? 'no working days at all, try reloading.' : null)
            ->emptyStateActions(!HK::exists() ? [
                Action::make('reload')
                    ->label('Reload')
                    ->action(function () {
                        Artisan::call('workday:update');
                        Notification::make()
                            ->title('Update Success!')
                            ->body('Successfully update workday list')
                            ->success()
                            ->send();
                    }),
            ] : [])
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

    public function render(): View
    {
        return view('livewire.tables.config.hari-kerja');
    }
}
