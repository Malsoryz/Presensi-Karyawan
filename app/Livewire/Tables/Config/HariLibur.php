<?php

namespace App\Livewire\Tables\Config;

use App\Models\HariLibur as HL;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\TextColumn;

use Illuminate\Support\HtmlString;
use Carbon\Carbon;

use Livewire\Component;

class HariLibur extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    // public ?array $monthQuery = HL::getInMonth()

    public function table(Table $table): Table
    {
        $defaultMonth = request()->get('month') ?? 1;

        return $table
            ->paginated(false)
            ->query(HL::query()->orderBy('tanggal'))
            ->columns([
                TextColumn::make('bulan')
                    ->label('Bulan'),
                TextColumn::make('nama')
                    ->label('Nama Hari Libur'),
                TextColumn::make('tanggal')
                    ->label('Tanggal')
            ])
            ->filters([
                SelectFilter::make('bulan')
                    ->default(Carbon::createFromDate(null, $defaultMonth, null)->translatedFormat('F'))
                    ->options([
                        'January' => 'Januari',
                        'February' => 'Februari',
                        'March' => 'Maret',
                        'April' => 'April',
                        'May' => 'Mei',
                        'June' => 'Juni',
                        'July' => 'Juli',
                        'August' => 'Agustus',
                        'September' => 'September',
                        'October' => 'Oktober',
                        'November' => 'November',
                        'December' => 'Desember'
                    ])
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
        return view('livewire.tables.config.hari-libur');
    }
}
