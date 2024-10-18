<?php

namespace App\Filament\Resources\DepartmentResource\Pages;

use App\Filament\Resources\DepartmentResource;
use App\Models\Department;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListDepartments extends ListRecords
{
    protected static string $resource = DepartmentResource::class;

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
                ->badge(Department::where('team_id', $tenant->id)
                    ->where('created_at', '>=', now()->subWeek())
                    ->count()),

            'This Month' => Tab::make()
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->where('created_at', '>=', now()->subMonth())
                        ->where('team_id', $tenant->id)
                )
                ->badge(Department::where('team_id', $tenant->id)
                    ->where('created_at', '>=', now()->subMonth())
                    ->count()),

            'This Year' => Tab::make()
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->where('created_at', '>=', now()->subYear())
                        ->where('team_id', $tenant->id)
                )
                ->badge(Department::where('team_id', $tenant->id)
                    ->where('created_at', '>=', now()->subYear())
                    ->count()),
        ];
    }
}
