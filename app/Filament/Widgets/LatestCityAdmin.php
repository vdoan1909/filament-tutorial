<?php

namespace App\Filament\Widgets;

use App\Models\City;
use Filament\Facades\Filament;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestCityAdmin extends BaseWidget
{
    protected static ?int $sort = 5;
    // protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $tenant = Filament::getTenant();

        return $table
            ->query(City::query()
                ->where('team_id', $tenant->id)
                ->orderByDesc('created_at')
                ->take(3))
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('state.name'),
                Tables\Columns\TextColumn::make('name'),
            ]);
    }
}
