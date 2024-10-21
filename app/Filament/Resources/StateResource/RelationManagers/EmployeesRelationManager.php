<?php

namespace App\Filament\Resources\StateResource\RelationManagers;

use App\Models\City;
use App\Models\Country;
use App\Models\Department;
use App\Models\State;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeesRelationManager extends RelationManager
{
    protected static string $relationship = 'employees';

    public function form(Form $form): Form
    {
        $tenant = Filament::getTenant();
        return $form
            ->schema([
                Forms\Components\Section::make('Relations')
                    ->description('Relations')
                    ->schema(
                        [
                            Forms\Components\Select::make('country_id')
                                ->label('Country ID')
                                ->relationship(name: 'country', titleAttribute: 'name')
                                ->options(
                                    fn() => Country::where('team_id', $tenant->id)
                                        ->pluck('name', 'id')
                                )
                                ->native(false)
                                ->searchable()
                                ->preload()
                                ->live()
                                ->afterStateUpdated(
                                    function (Set $set) {
                                        $set('state_id', null);
                                        $set('city_id', null);
                                    }
                                ),

                            Forms\Components\Select::make('state_id')
                                ->label('State ID')
                                ->options(
                                    fn(Get $get) => State::where('country_id', $get('country_id'))
                                        ->pluck('name', 'id')
                                )
                                ->native(false)
                                ->searchable()
                                ->preload()
                                ->live()
                                ->afterStateUpdated(fn(Set $set) => $set('city_id', null)),

                            Forms\Components\Select::make('city_id')
                                ->label('City ID')
                                ->options(
                                    fn(Get $get) => City::where('state_id', $get('state_id'))
                                        ->pluck('name', 'id')
                                )
                                ->native(false)
                                ->searchable()
                                ->preload()
                                ->live(),

                            Forms\Components\Select::make('department_id')
                                ->label('Department ID')
                                ->relationship(name: 'department', titleAttribute: 'name')
                                ->options(
                                    fn() => Department::where('team_id', $tenant->id)
                                        ->pluck('name', 'id')
                                )
                                ->native(false)
                                ->searchable()
                                ->preload()
                        ]
                    )->columns(2),

                Forms\Components\Section::make('User Name')
                    ->description('Enter your name')
                    ->schema(
                        [
                            Forms\Components\TextInput::make('first_name')
                                ->required()
                                ->maxLength(20),
                            Forms\Components\TextInput::make('middle_name')
                                ->required()
                                ->maxLength(20),
                            Forms\Components\TextInput::make('last_name')
                                ->required()
                                ->maxLength(20),
                        ]
                    )->columns(3),

                Forms\Components\Section::make('Address')
                    ->description('Enter address')
                    ->schema(
                        [
                            Forms\Components\TextInput::make('address')
                                ->required(),
                            Forms\Components\TextInput::make('zip_code')
                                ->required()
                                ->maxLength(255),
                        ]
                    )->columns(2),

                Forms\Components\Section::make('User Date')
                    ->description('Your date')
                    ->schema(
                        [
                            Forms\Components\DatePicker::make('date_of_birth')
                                ->required()
                                ->native(false),
                            Forms\Components\DatePicker::make('date_hired')
                                ->required()
                                ->native(false),
                        ]
                    )->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('first_name')
            ->columns([
                Tables\Columns\TextColumn::make('country.name')
                    ->label('Country Name')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable(),
                Tables\Columns\TextColumn::make('state.name')
                    ->label(label: 'State Name')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->label('City Name')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('department.name')
                    ->label('Department Name')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('middle_name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('zip_code')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('date_of_birth')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_hired')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('country_id')
                    ->label('Filter By Country Name')
                    ->options(function () {
                        $tenant = Filament::getTenant();

                        return Country::where('team_id', $tenant->id)
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->preload(),

                \Filament\Tables\Filters\Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('created_from')
                            ->native(false),
                        \Filament\Forms\Components\DatePicker::make('created_until')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $tenant = Filament::getTenant();

                        return $query
                            ->where('team_id', $tenant->id)
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            $indicators[] = \Filament\Tables\Filters\Indicator::make('Created from ' . \Carbon\Carbon::parse($data['created_from'])->toFormattedDateString())
                                ->removeField('created_from');
                        }

                        if ($data['created_until'] ?? null) {
                            $indicators[] = \Filament\Tables\Filters\Indicator::make('Created until ' . \Carbon\Carbon::parse($data['created_until'])->toFormattedDateString())
                                ->removeField('created_until');
                        }

                        return $indicators;
                    })
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
