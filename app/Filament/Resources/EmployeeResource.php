<?php

namespace App\Filament\Resources;

use Filament\Facades\Filament;
use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\Country;
use App\Models\City;
use App\Models\Department;
use App\Models\Employee;
use App\Models\State;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'User Management';
    protected static ?string $navigationLabel = 'Employees Navigation Label';
    protected static ?string $modelLabel = 'Employees Model Label';
    protected static ?string $slug = 'employees-slug';

    // global search theo first_name
    protected static ?string $recordTitleAttribute = 'first_name';

    // tiêu đề tìm kiếm là last_name
    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return 'Employee: ' . $record->first_name . ' ' . $record->middle_name . ' ' . $record->last_name;
    }

    // tìm trên nhiều cột
    // public static function getGloballySearchableAttributes(): array
    // {
    //     return [
    //         'first_name',
    //         'middle_name',
    //         'last_name'
    //     ];
    // }

    // kết quả trả về hiển thị dạng
    // 'Country name' => $record->country->name,
    //         'First name' => $record->first_name,
    //         'Middle name' => $record->middle_name,
    //         'Last name' => $record->last_name,
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Country name' => $record->country->name,
            'First name' => $record->first_name,
            'Middle name' => $record->middle_name,
            'Last name' => $record->last_name,
        ];
    }

    // đếm số bản khi hiện có
    public static function getNavigationBadge(): ?string
    {
        $tenant = Filament::getTenant();

        return static::getModel()::where('team_id', $tenant->id)->count();
    }

    // tùy chỉnh màu của badge
    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
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

    public static function table(Table $table): Table
    {
        return $table
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
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                \Filament\Infolists\Components\Section::make('Employee Info')
                    ->schema(
                        [
                            \Filament\Infolists\Components\Section::make('Employee Relations')
                                ->schema(
                                    [
                                        \Filament\Infolists\Components\TextEntry::make('country.name')
                                            ->label('Country Name'),
                                        \Filament\Infolists\Components\TextEntry::make('state.name')
                                            ->label('State Name'),
                                        \Filament\Infolists\Components\TextEntry::make('city.name')
                                            ->label('City Name'),
                                        \Filament\Infolists\Components\TextEntry::make('department.name')
                                            ->label('Department Name'),
                                    ]
                                )->columns(2),

                            \Filament\Infolists\Components\Section::make('Employee Name')
                                ->schema([
                                    \Filament\Infolists\Components\TextEntry::make('first_name')
                                        ->label('First Name'),
                                    \Filament\Infolists\Components\TextEntry::make('middle_name')
                                        ->label('Middle Name'),
                                    \Filament\Infolists\Components\TextEntry::make('last_name')
                                        ->label('Last Name'),
                                ])->columns(3),

                            \Filament\Infolists\Components\Section::make('Employee Address')
                                ->schema([
                                    \Filament\Infolists\Components\TextEntry::make('address')
                                        ->label('Address'),
                                    \Filament\Infolists\Components\TextEntry::make('zip_code')
                                        ->label('Zip Code'),
                                ])->columns(2),

                            \Filament\Infolists\Components\Section::make('Employee Date')
                                ->schema([
                                    \Filament\Infolists\Components\TextEntry::make('date_of_birth')
                                        ->label('Date of Birth'),
                                    \Filament\Infolists\Components\TextEntry::make('date_hired')
                                        ->label('Date Hired'),
                                ])->columns(2)

                        ]
                    )
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            // 'view' => Pages\ViewEmployee::route('/{record}'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
