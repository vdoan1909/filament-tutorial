<?php

namespace App\Filament\App\Widgets;

use Filament\Widgets\ChartWidget;

class UserAppChart extends ChartWidget
{
    protected static ?string $heading = 'User Chart';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'User created',
                    'data' => [0, 10, 5, 2, 21, 32, 45, 74, 65, 45, 77, 89],
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
