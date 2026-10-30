<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PaymentType: string implements HasLabel
{
    case Advance = 'advance';
    case Payment = 'payment';
    case Refund = 'refund';

    public function getLabel(): string
    {
        return match ($this) {
            self::Advance => 'Advance',
            self::Payment => 'Payment',
            self::Refund => 'Refund',
        };
    }
}
