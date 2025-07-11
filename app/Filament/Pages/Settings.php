<?php

namespace App\Filament\Pages;

use App\Models\Config;
use App\Models\HariKerja;
use App\Models\HariLibur;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Pages\Page;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Actions;

use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Placeholder;

use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\HtmlString;
use Carbon\Carbon;

class Settings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.settings';

    public $currentTab = 'tab1';

    public function saveChanges(): void
    {
        $this->dispatch('save-jam-kerja');
    }
}
