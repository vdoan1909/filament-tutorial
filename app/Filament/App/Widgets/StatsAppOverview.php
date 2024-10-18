<?php

namespace App\Filament\App\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Support\Enums\IconPosition;

use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsAppOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Users', User::count())
                ->description('All users in the database')
                ->descriptionIcon('heroicon-o-user-group', IconPosition::Before)
                ->color('info'),
        ];
    }
}
