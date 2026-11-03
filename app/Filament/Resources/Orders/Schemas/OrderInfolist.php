<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Filament\Infolists\HistoryTab;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make([
                    Tabs::make('Order')
                        ->persistTabInQueryString()
                        ->tabs([
                            Tab::make('Overview')
                                ->schema([
                                    Section::make('Order')
                                        ->columns(2)
                                        ->schema([
                                            TextEntry::make('customer.user.name')
                                                ->label('Customer'),
                                            TextEntry::make('customer.customer_code')
                                                ->label('Customer code'),
                                            TextEntry::make('notes')
                                                ->placeholder('—')
                                                ->columnSpanFull(),
                                        ]),
                                    Section::make('Totals')
                                        ->columns(4)
                                        ->schema([
                                            TextEntry::make('subtotal')
                                                ->numeric(decimalPlaces: 2),
                                            TextEntry::make('discount')
                                                ->numeric(decimalPlaces: 2),
                                            TextEntry::make('tax')
                                                ->numeric(decimalPlaces: 2),
                                            TextEntry::make('total')
                                                ->numeric(decimalPlaces: 2)
                                                ->weight('bold'),
                                        ]),
                                ]),
                            Tab::make('Payments')
                                ->icon('heroicon-o-banknotes')
                                ->schema([
                                    RepeatableEntry::make('payments')
                                        ->hiddenLabel()
                                        ->placeholder('No payments recorded.')
                                        ->columns(5)
                                        ->schema([
                                            TextEntry::make('amount')
                                                ->numeric(decimalPlaces: 2),
                                            TextEntry::make('method')
                                                ->badge(),
                                            TextEntry::make('type')
                                                ->badge(),
                                            TextEntry::make('paid_at')
                                                ->dateTime(),
                                            TextEntry::make('reference')
                                                ->placeholder('—'),
                                        ]),
                                ]),
                            HistoryTab::make(withDeleted: true),
                        ]),
                ])->columnSpan(2),
                Group::make([
                    Section::make('Status')
                        ->schema([
                            TextEntry::make('order_number'),
                            TextEntry::make('status')
                                ->badge(),
                        ]),
                ])->columnSpan(1),
            ]);
    }
}
