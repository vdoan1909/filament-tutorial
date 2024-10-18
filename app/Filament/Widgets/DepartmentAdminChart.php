<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class DepartmentAdminChart extends ChartWidget
{
    protected static ?string $heading = 'Department Chart';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Departments created',
                    'data' => [25, 150, 88, 212, 33, 97, 245, 170, 12, 250, 140, 60],
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
