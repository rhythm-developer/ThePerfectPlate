<?php

namespace App\Filament\Resources\OrderEvents\Pages;

use App\Filament\Resources\OrderEvents\OrderEventResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrderEvent extends CreateRecord
{
    protected static string $resource = OrderEventResource::class;
}
