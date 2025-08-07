<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Component;
use Illuminate\View\View;
use Filament\Actions\Action;

class ViewLivewire extends Component
{
    protected $class = null;
    protected $action = null;

    protected string $view = 'forms.components.view-livewire';

    final public function __construct(string $class)
    {
        $this->class = $class;
    }

    public static function make(string $class): static
    {
        return app(static::class, ['class' => $class]);
    }

    public function render(): View
    {
        return view('forms.components.view-livewire', [
            'class' => $this->class, 
            'viewData' => $this->getViewData(),
        ]);
    }
}
