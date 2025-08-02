<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;
use App\Models\Config;

class TermOfServices extends Field
{
    protected string $view = 'forms.components.term-of-services';

    public function getTermOfServices(): ?string
    {
        return Config::get('short_term_of_service');
    }
}
