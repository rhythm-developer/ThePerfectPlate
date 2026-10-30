<?php

namespace App\Filament\Resources\OrderEvents;

use App\Filament\Resources\OrderEvents\Pages\CreateOrderEvent;
use App\Filament\Resources\OrderEvents\Pages\EditOrderEvent;
use App\Filament\Resources\OrderEvents\Pages\ListOrderEvents;
use App\Filament\Resources\OrderEvents\RelationManagers\EmployeesRelationManager;
use App\Filament\Resources\OrderEvents\RelationManagers\MenuItemsRelationManager;
use App\Filament\Resources\OrderEvents\Schemas\OrderEventForm;
use App\Filament\Resources\OrderEvents\Tables\OrderEventsTable;
use App\Models\OrderEvent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OrderEventResource extends Resource
{
    protected static ?string $model = OrderEvent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    /**
     * Reached only via an order's "Manage menu & staff" action, not left nav.
     */
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return OrderEventForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrderEventsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            MenuItemsRelationManager::class,
            EmployeesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrderEvents::route('/'),
            'create' => CreateOrderEvent::route('/create'),
            'edit' => EditOrderEvent::route('/{record}/edit'),
        ];
    }
}
