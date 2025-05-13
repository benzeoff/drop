<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use App\Models\User;
use App\Http\Middleware\Authenticate; // Добавляем импорт

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->authGuard('web')
            ->authMiddleware([
                Authenticate::class, // Теперь класс определён
            ])
            ->middleware([
                \App\Http\Middleware\Admin::class,
            ])
            ->userMenuItems([
                'logout',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->navigationGroups([
                'Users',
                'Bookings',
            ]);
    }
}
