<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

use App\Models\Config;
use App\Filament\Pages\Dashboard;
use App\Filament\Widgets\PresenceData;

use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Filament\Support\Facades\FilamentView;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Storage;

use App\Livewire as Component;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::USER_MENU_BEFORE,
            fn (): string => $this->renderComponents([
                Component\DateTime::class,
            ]),
        );

        return $panel
            ->brandName(fn () => new HtmlString($this->appBrand()))
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // PresenceData::class,
            ])
            ->globalSearch(false)
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    protected function appBrand(): string
    {
        $brandName = Config::get('app_brand');
        $brandLogo = Config::get('app_logo');
        $logo = $brandLogo ? Storage::disk('public')->url($brandLogo) : null;

        return Blade::render(<<<'BLADE'
            @if($brandName || $brandLogo)
                <div class="flex flex-row gap-3 justify-center items-center">
                    @if($brandLogo)
                        <img 
                            alt="{{ $brandName ?? 'App Logo' }}" 
                            src="{{ $brandLogo }}" 
                            class="fi-logo h-6" 
                        />
                    @endif
                    @if($brandName)
                        <div class="fi-logo text-xl font-bold leading-5 tracking-tight text-gray-950 dark:text-white">
                            {{ $brandName }}
                        </div>
                    @endif
                </div>
            @endif
        BLADE, [
            'brandName' => $brandName,
            'brandLogo' => $logo
        ]);
    }

    protected function renderComponents(array $components = []): string
    {
        $collection = collect($components)
            ->map(fn ($item) => is_string($item) ? app($item) : $item)
            ->filter(fn ($item) => $item instanceof \Livewire\Component)
            ->values();

        return Blade::render(<<<'BLADE'
            <div class="flex flex-row gap-1">
                @foreach($components as $component)
                    @livewire($component)
                @endforeach
            </div>
        BLADE, ['components' => $collection]);
    }
}
