<?php

namespace App\Filament\Resources\OrderEvents\Pages;

use App\Filament\Resources\OrderEvents\OrderEventResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewOrderEvent extends ViewRecord
{
    protected static string $resource = OrderEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
