<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum RoleName: string implements HasColor, HasLabel
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

    public function getColor(): string
    {
        return match ($this) {
            self::Admin => 'danger',
            self::Customer => 'success',
            self::Employee => 'info',
        };
    }
}
