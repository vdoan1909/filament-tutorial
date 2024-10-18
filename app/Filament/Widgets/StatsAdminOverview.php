<?php

namespace App\Filament\Widgets;

use App\Models\City;
use App\Models\Department;
use App\Models\State;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsAdminOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $tenant = Filament::getTenant();
        return [
            Stat::make('State', State::where('team_id', $tenant->id)->count())
                ->description('All states in this team')
                ->descriptionIcon('heroicon-o-building-library')
                ->chart([7, 2, 10, 3, 15, 4, 17]) // cai nay` demo du lieu ao
                ->color('info'),

            Stat::make('City', City::where('team_id', $tenant->id)->count())
                ->description('All cities in this team')
                ->descriptionIcon('heroicon-o-building-office-2')
                ->chart([2, 10, 5, 3, 6, 4, 19])
                ->color('info'),

            Stat::make('Department', Department::where('team_id', $tenant->id)->count())
                ->description('All departments in this team')
                ->descriptionIcon('heroicon-o-banknotes')
                ->chart([1, 2, 8, 3, 6, 4, 17])
                ->color('info'),
        ];
    }
}
