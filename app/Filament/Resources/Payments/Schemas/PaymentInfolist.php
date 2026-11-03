<?php

namespace App\Filament\Resources\Payments\Schemas;

use App\Filament\Infolists\HistoryTab;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class PaymentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Payment')
                    ->persistTabInQueryString()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Details')
                            ->schema([
                                Section::make('Payment')
                                    ->columns(2)
                                    ->schema([
                                        TextEntry::make('order.order_number')
                                            ->label('Order'),
                                        TextEntry::make('amount')
                                            ->numeric(decimalPlaces: 2),
                                        TextEntry::make('method')
                                            ->badge(),
                                        TextEntry::make('type')
                                            ->badge(),
                                    ]),
                                Section::make('Details')
                                    ->columns(2)
                                    ->schema([
                                        TextEntry::make('reference')
                                            ->placeholder('—'),
                                        TextEntry::make('paid_at')
                                            ->dateTime(),
                                        TextEntry::make('receivedBy.user.name')
                                            ->label('Received by')
                                            ->placeholder('—'),
                                        TextEntry::make('notes')
                                            ->placeholder('—')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                        HistoryTab::make(),
                    ]),
            ]);
    }
}
