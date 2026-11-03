<?php

namespace App\Filament\Resources\OrderEvents\Schemas;

use App\Enums\OrderEventStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class OrderEventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Event')
                    ->persistTabInQueryString()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Details')
                            ->columns(2)
                            ->schema([
                                Select::make('order_id')
                                    ->relationship('order', 'order_number')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required(),
                                TextInput::make('name')
                                    ->required(),
                                DatePicker::make('event_date')
                                    ->required(),
                                TimePicker::make('start_time'),
                                TimePicker::make('end_time'),
                                TextInput::make('guest_count')
                                    ->required()
                                    ->numeric(),
                                Select::make('status')
                                    ->options(OrderEventStatus::class)
                                    ->default('pending')
                                    ->required(),
                                TextInput::make('subtotal')
                                    ->numeric()
                                    ->default(0.0)
                                    ->disabled()
                                    ->dehydrated()
                                    ->helperText('Calculated from this event\'s menu item lines.'),
                            ]),
                        Tab::make('Venue')
                            ->schema([
                                TextInput::make('venue')
                                    ->default(null),
                                Textarea::make('venue_address')
                                    ->default(null)
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Notes')
                            ->schema([
                                Textarea::make('notes')
                                    ->default(null)
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }
}
