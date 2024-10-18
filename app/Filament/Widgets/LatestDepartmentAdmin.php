<?php

namespace App\Filament\Widgets;

use App\Models\Department;
use Filament\Facades\Filament;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestDepartmentAdmin extends BaseWidget
{
    protected static ?int $sort = 5;
    // protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $tenant = Filament::getTenant();
        return $table
            ->query(Department::query()
                ->where('team_id', $tenant->id)
                ->orderByDesc('created_at')
                ->take(3))
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('name'),
            ]);
    }
}
