<?php

namespace App\Filament\Resources\Customers\Schemas;

use App\Filament\Infolists\HistoryTab;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class CustomerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make([
                    Tabs::make('Customer')
                        ->persistTabInQueryString()
                        ->tabs([
                            Tab::make('Details')
                                ->schema([
                                    Section::make('Account')
                                        ->columns(2)
                                        ->schema([
                                            TextEntry::make('user.name')
                                                ->label('Name'),
                                            TextEntry::make('user.email')
                                                ->label('Email'),
                                            TextEntry::make('user.phone')
                                                ->label('Phone')
                                                ->placeholder('—'),
                                        ]),
                                    Section::make('Company')
                                        ->schema([
                                            TextEntry::make('company_name')
                                                ->placeholder('—'),
                                            TextEntry::make('notes')
                                                ->placeholder('—'),
                                        ]),
                                ]),
                            HistoryTab::make(),
                        ]),
                ])->columnSpan(2),
                Group::make([
                    Section::make('Status')
                        ->schema([
                            TextEntry::make('customer_code'),
                            IconEntry::make('is_active')
                                ->boolean(),
                        ]),
                ])->columnSpan(1),
            ]);
    }
}
