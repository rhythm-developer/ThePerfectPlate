<?php

namespace App\Filament\Resources\OrderEvents\Pages;

use App\Filament\Resources\OrderEvents\OrderEventResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOrderEvents extends ListRecords
{
    protected static string $resource = OrderEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
