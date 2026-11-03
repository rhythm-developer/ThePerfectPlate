<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Enums\OrderEventStatus;
use App\Enums\OrderStatus;
use App\Models\MenuItem;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Order')
                    ->persistTabInQueryString()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Order')
                            ->columns(2)
                            ->schema([
                                TextInput::make('order_number')
                                    ->placeholder('Auto-generated')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->visibleOn('edit'),
                                Select::make('customer_id')
                                    ->relationship('customer', 'customer_code')
                                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->customer_code} — {$record->user?->name}")
                                    ->searchable(['customer_code'])
                                    ->preload()
                                    ->required(),
                                Select::make('status')
                                    ->options(OrderStatus::class)
                                    ->default(OrderStatus::Pending)
                                    ->required(),
                                Textarea::make('notes')
                                    ->default(null)
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Events')
                            ->schema([
                                self::eventsRepeater(),
                            ]),
                        Tab::make('Totals')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('subtotal_preview')
                                    ->label('Subtotal')
                                    ->state(fn (Get $get) => number_format(self::orderSubtotal($get('events')), 2))
                                    ->helperText('Sum of all event menu items.'),
                                TextInput::make('discount')
                                    ->required()
                                    ->numeric()
                                    ->default(0.0)
                                    ->live(onBlur: true),
                                TextInput::make('tax')
                                    ->required()
                                    ->numeric()
                                    ->default(0.0)
                                    ->live(onBlur: true),
                                TextEntry::make('total_preview')
                                    ->label('Total')
                                    ->weight('bold')
                                    ->state(fn (Get $get) => number_format(
                                        self::orderSubtotal($get('events')) - (float) $get('discount') + (float) $get('tax'),
                                        2,
                                    ))
                                    ->helperText('Subtotal − discount + tax.'),
                            ]),
                    ]),
            ]);
    }

    protected static function eventsRepeater(): Repeater
    {
        return Repeater::make('events')
            ->relationship()
            ->hiddenLabel()
            ->defaultItems(1)
            ->collapsible()
            ->addActionLabel('Add event')
            ->itemLabel(fn (array $state) => filled($state['name'] ?? null)
                ? $state['name'].(filled($state['event_date'] ?? null) ? ' — '.Carbon::parse($state['event_date'])->format('d M Y') : '')
                : 'New event')
            ->columns(2)
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->live(onBlur: true),
                DatePicker::make('event_date')
                    ->required()
                    ->live(),
                TimePicker::make('start_time'),
                TimePicker::make('end_time'),
                TextInput::make('guest_count')
                    ->required()
                    ->numeric(),
                Select::make('status')
                    ->options(OrderEventStatus::class)
                    ->default(OrderEventStatus::Pending)
                    ->required(),
                TextInput::make('venue')
                    ->default(null),
                Textarea::make('venue_address')
                    ->default(null),
                Textarea::make('notes')
                    ->default(null)
                    ->columnSpanFull(),
                self::menuItemLinesRepeater(),
                self::staffRepeater(),
                TextEntry::make('event_subtotal_preview')
                    ->label('Event subtotal')
                    ->weight('bold')
                    ->state(fn (Get $get) => number_format(self::linesTotal($get('menuItemLines')), 2)),
            ]);
    }

    protected static function menuItemLinesRepeater(): Repeater
    {
        return Repeater::make('menuItemLines')
            ->label('Menu items')
            ->relationship()
            ->defaultItems(0)
            ->addActionLabel('Add menu item')
            ->columnSpanFull()
            ->table([
                TableColumn::make('Menu item'),
                TableColumn::make('Qty'),
                TableColumn::make('Unit price'),
                TableColumn::make('Line total'),
                TableColumn::make('Notes'),
            ])
            ->schema([
                Select::make('menu_item_id')
                    ->relationship('menuItem', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                    ->live()
                    ->afterStateUpdated(fn ($state, Set $set) => $set('unit_price', MenuItem::find($state)?->price)),
                TextInput::make('quantity')
                    ->numeric()
                    ->minValue(1)
                    ->default(1)
                    ->required()
                    ->live(onBlur: true),
                TextInput::make('unit_price')
                    ->numeric()
                    ->minValue(0)
                    ->required()
                    ->live(onBlur: true),
                TextEntry::make('line_total_preview')
                    ->state(fn (Get $get) => number_format((float) $get('quantity') * (float) $get('unit_price'), 2)),
                TextInput::make('notes'),
            ]);
    }

    protected static function staffRepeater(): Repeater
    {
        return Repeater::make('staffAssignments')
            ->label('Staff')
            ->relationship()
            ->defaultItems(0)
            ->addActionLabel('Add staff')
            ->columnSpanFull()
            ->table([
                TableColumn::make('Employee'),
                TableColumn::make('Role on event'),
            ])
            ->schema([
                Select::make('employee_id')
                    ->relationship('employee', 'employee_code')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->employee_code} — {$record->user?->name}")
                    ->searchable(['employee_code'])
                    ->preload()
                    ->required()
                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                TextInput::make('role'),
            ]);
    }

    /**
     * @param  array<array-key, array<string, mixed>>|null  $lines
     */
    protected static function linesTotal(?array $lines): float
    {
        return collect($lines ?? [])
            ->sum(fn (array $line) => (float) ($line['quantity'] ?? 0) * (float) ($line['unit_price'] ?? 0));
    }

    /**
     * @param  array<array-key, array<string, mixed>>|null  $events
     */
    protected static function orderSubtotal(?array $events): float
    {
        return collect($events ?? [])
            ->sum(fn (array $event) => self::linesTotal($event['menuItemLines'] ?? []));
    }
}
