<?php

namespace App\Filament\Admin\Resources\ComputerScienceResource\Widgets;

use App\Models\ComputerScienceResource;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class ResourcesTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Resources Trend Chart';

    protected function getData(): array
    {
        $data = Trend::model(ComputerScienceResource::class)
            ->between(
                start: now()->subYear(),
                end: now(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Computer Science Resources',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
