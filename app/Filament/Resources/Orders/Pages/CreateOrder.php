<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    /**
     * Runs after the events, menu items and staff repeaters have been saved.
     */
    protected function afterCreate(): void
    {
        $this->record->recalculateAllTotals();
    }
}
