<?php

namespace App\Filament\Widgets;

use App\Models\State;
use Filament\Facades\Filament;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestStateAdmin extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = 'full';
    // responsive
    // protected int | string | array $columnSpan = [
    //     'md' => 2,
    //     'xl' => 3,
    // ];

    public function table(Table $table): Table
    {
        $tenant = Filament::getTenant();
        return $table
            ->query(State::query()
                ->where('team_id', $tenant->id)
                ->orderByDesc('created_at')
                ->take(3))
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('country.name'),
                Tables\Columns\TextColumn::make('name'),
            ]);
    }
}
