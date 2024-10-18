<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

// use Flowframe\Trend\Trend;
// use Flowframe\Trend\TrendValue;

class StateAdminChart extends ChartWidget
{
    protected static ?string $heading = 'State Chart';
    // protected static string $color = 'warning';
    protected static ?int $sort = 1;


    // ham` test 
    // protected function getData(): array
    // {
    //     $data = Trend::model(State::class)
    //         ->between(
    //             start: now()->startOfMonth(),
    //             end: now()->endOfMonth(),
    //         )
    //         ->perMonth()
    //         ->count();

    //     return [
    //         'datasets' => [
    //             [
    //                 'label' => 'States',
    //                 'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
    //             ],
    //         ],
    //         'labels' => $data->map(fn(TrendValue $value) => $value->date),
    //     ];
    // }

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'States created',
                    'data' => [77, 135, 220, 45, 98, 189, 56, 260, 14, 112, 300, 34],
                    // 'backgroundColor' => '#36A2EB',
                    // 'borderColor' => '#9BD0F5',
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getType(): string
    {
        return 'radar';
    }
}
