<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/**
 * Sidebar groups, shown in case order.
 */
enum NavigationGroup: string implements HasLabel
{
    case Menu = 'menu';
    case Orders = 'orders';
    case People = 'people';

    public function getLabel(): string
    {
        return match ($this) {
            self::Menu => 'Menu',
            self::Orders => 'Orders',
            self::People => 'People',
        };
    }
}
