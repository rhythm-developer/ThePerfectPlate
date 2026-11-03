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
                            Tab::make('Events')
                                ->icon('heroicon-o-calendar-days')
                                ->schema([
                                    RepeatableEntry::make('events')
                                        ->hiddenLabel()
                                        ->placeholder('No events.')
                                        ->columns(4)
                                        ->schema([
                                            TextEntry::make('name'),
                                            TextEntry::make('event_date')
                                                ->date(),
                                            TextEntry::make('guest_count')
                                                ->label('Guests')
                                                ->numeric(),
                                            TextEntry::make('status')
                                                ->badge(),
                                            TextEntry::make('venue')
                                                ->placeholder('—'),
                                            TextEntry::make('start_time')
                                                ->time()
                                                ->placeholder('—'),
                                            TextEntry::make('end_time')
                                                ->time()
                                                ->placeholder('—'),
                                            TextEntry::make('subtotal')
                                                ->numeric(decimalPlaces: 2)
                                                ->weight('bold'),
                                            RepeatableEntry::make('menuItemLines')
                                                ->label('Menu items')
                                                ->placeholder('No menu items.')
                                                ->columnSpanFull()
                                                ->columns(4)
                                                ->schema([
                                                    TextEntry::make('menuItem.name')
                                                        ->label('Item'),
                                                    TextEntry::make('quantity')
                                                        ->label('Qty')
                                                        ->numeric(),
                                                    TextEntry::make('unit_price')
                                                        ->numeric(decimalPlaces: 2),
                                                    TextEntry::make('line_total')
                                                        ->numeric(decimalPlaces: 2),
                                                ]),
                                            RepeatableEntry::make('staffAssignments')
                                                ->label('Staff')
                                                ->placeholder('No staff assigned.')
                                                ->columnSpanFull()
                                                ->columns(3)
                                                ->schema([
                                                    TextEntry::make('employee.employee_code')
                                                        ->label('Code'),
                                                    TextEntry::make('employee.user.name')
                                                        ->label('Name'),
                                                    TextEntry::make('role')
                                                        ->label('Role on event')
                                                        ->placeholder('—'),
                                                ]),
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
