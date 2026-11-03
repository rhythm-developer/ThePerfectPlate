<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Filament\Infolists\HistoryTab;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('User')
                    ->persistTabInQueryString()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Profile')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('name'),
                                TextEntry::make('email'),
                                TextEntry::make('phone')
                                    ->placeholder('—'),
                                TextEntry::make('address')
                                    ->placeholder('—')
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Access')
                            ->schema([
                                TextEntry::make('role.name')
                                    ->label('Role')
                                    ->badge()
                                    ->placeholder('No role'),
                            ]),
                        Tab::make('Linked records')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('customer.customer_code')
                                    ->label('Customer code')
                                    ->placeholder('—'),
                                TextEntry::make('employee.employee_code')
                                    ->label('Employee code')
                                    ->placeholder('—'),
                            ]),
                        HistoryTab::make(),
                    ]),
            ]);
    }
}
