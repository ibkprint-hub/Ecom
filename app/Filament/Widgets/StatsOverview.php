<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $today = Order::whereDate('created_at', today())->count();
        $pending = Order::where('status', 'new')->count();
        $revenue = Order::where('status', 'delivered')->sum('total');

        return [
            Stat::make('Commandes aujourd\'hui', $today)
                ->description('Nouvelles commandes du jour')
                ->color('primary'),
            Stat::make('À confirmer', $pending)
                ->description('En attente de traitement')
                ->color('warning'),
            Stat::make('CA livré', number_format((float) $revenue, 0, ',', ' ') . ' DZD')
                ->description('Commandes livrées (encaissées)')
                ->color('success'),
        ];
    }
}
