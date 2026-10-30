<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum RoleName: string implements HasLabel
{
    case Admin = 'admin';
    case Customer = 'customer';
    case Employee = 'employee';

    public function getLabel(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Customer => 'Customer',
            self::Employee => 'Employee',
        };
    }
}
