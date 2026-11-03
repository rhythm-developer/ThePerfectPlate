<?php

namespace App\Filament\Resources\OrderEvents\Schemas;

use App\Filament\Infolists\HistoryTab;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class OrderEventInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make([
                    Tabs::make('Event')
                        ->persistTabInQueryString()
                        ->tabs([
                            Tab::make('Details')
                                ->columns(2)
                                ->schema([
                                    TextEntry::make('name'),
                                    TextEntry::make('event_date')
                                        ->date(),
                                    TextEntry::make('start_time')
                                        ->time()
                                        ->placeholder('—'),
                                    TextEntry::make('end_time')
                                        ->time()
                                        ->placeholder('—'),
                                    TextEntry::make('guest_count')
                                        ->numeric(),
                                ]),
                            Tab::make('Venue')
                                ->schema([
                                    TextEntry::make('venue')
                                        ->placeholder('—'),
                                    TextEntry::make('venue_address')
                                        ->placeholder('—'),
                                ]),
                            Tab::make('Notes')
                                ->schema([
                                    TextEntry::make('notes')
                                        ->hiddenLabel()
                                        ->placeholder('No notes.'),
                                ]),
                            HistoryTab::make(),
                        ]),
                ])->columnSpan(2),
                Group::make([
                    Section::make('Status')
                        ->schema([
                            TextEntry::make('order.order_number')
                                ->label('Order'),
                            TextEntry::make('status')
                                ->badge(),
                            TextEntry::make('subtotal')
                                ->numeric(decimalPlaces: 2),
                        ]),
                ])->columnSpan(1),
            ]);
    }
}
