<?php

namespace App\Filament\Resources\Employees\Schemas;

use App\Filament\Infolists\HistoryTab;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class EmployeeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make([
                    Tabs::make('Employee')
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
                                    Section::make('Employment')
                                        ->columns(2)
                                        ->schema([
                                            TextEntry::make('designation'),
                                            TextEntry::make('hire_date')
                                                ->date()
                                                ->placeholder('—'),
                                            TextEntry::make('salary')
                                                ->numeric()
                                                ->placeholder('—'),
                                        ]),
                                ]),
                            HistoryTab::make(),
                        ]),
                ])->columnSpan(2),
                Group::make([
                    Section::make('Status')
                        ->schema([
                            TextEntry::make('employee_code'),
                            IconEntry::make('is_active')
                                ->boolean(),
                        ]),
                ])->columnSpan(1),
            ]);
    }
}
