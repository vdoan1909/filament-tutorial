<?php

namespace App\Filament\Resources\CountryResource\Pages;

use App\Filament\Resources\CountryResource;
use App\Models\Country;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Facades\Filament;

class ListCountries extends ListRecords
{
    protected static string $resource = CountryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $tenant = Filament::getTenant();

        return [
            'All' => Tab::make()
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->where('team_id', $tenant->id)
                ),

            'This Week' => Tab::make()
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->where('created_at', '>=', now()->subWeek())
                        ->where('team_id', $tenant->id)
                )
                ->badge(Country::where('team_id', $tenant->id)
                    ->where('created_at', '>=', now()->subWeek())
                    ->count()),

            'This Month' => Tab::make()
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->where('created_at', '>=', now()->subMonth())
                        ->where('team_id', $tenant->id)
                )
                ->badge(Country::where('team_id', $tenant->id)
                    ->where('created_at', '>=', now()->subMonth())
                    ->count()),

            'This Year' => Tab::make()
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->where('created_at', '>=', now()->subYear())
                        ->where('team_id', $tenant->id)
                )
                ->badge(Country::where('team_id', $tenant->id)
                    ->where('created_at', '>=', now()->subYear())
                    ->count()),
        ];
    }
}
