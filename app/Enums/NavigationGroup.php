<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/**
 * Sidebar groups, shown in case order.
 */
enum NavigationGroup: string implements HasLabel
{
    case People = 'people';
    case Orders = 'orders';
    case Menu = 'menu';

    public function getLabel(): string
    {
        return match ($this) {
            self::People => 'People',
            self::Orders => 'Orders',
            self::Menu => 'Menu',
        };
    }
}
