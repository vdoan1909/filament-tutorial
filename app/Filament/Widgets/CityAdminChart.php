<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class CityAdminChart extends ChartWidget
{
    protected static ?string $heading = 'City Chart';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Cities created',
                    'data' => [120, 114, 30, 12, 126, 192, 270, 444, 390, 270, 462, 534],
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
