<?php

namespace App\Filament\Resources\MenuItems\Schemas;

use App\Filament\Infolists\HistoryTab;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class MenuItemInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make([
                    Tabs::make('Menu item')
                        ->persistTabInQueryString()
                        ->tabs([
                            Tab::make('Details')
                                ->schema([
                                    Section::make('Details')
                                        ->columns(2)
                                        ->schema([
                                            TextEntry::make('menuCategory.name')
                                                ->label('Category'),
                                            TextEntry::make('name'),
                                            TextEntry::make('price')
                                                ->money(),
                                            TextEntry::make('description')
                                                ->placeholder('—')
                                                ->columnSpanFull(),
                                        ]),
                                    Section::make('Image')
                                        ->schema([
                                            ImageEntry::make('image')
                                                ->hiddenLabel()
                                                ->placeholder('No image'),
                                        ]),
                                ]),
                            HistoryTab::make(),
                        ]),
                ])->columnSpan(2),
                Group::make([
                    Section::make('Status')
                        ->schema([
                            IconEntry::make('is_available')
                                ->boolean(),
                            IconEntry::make('is_vegetarian')
                                ->boolean(),
                        ]),
                ])->columnSpan(1),
            ]);
    }
}
