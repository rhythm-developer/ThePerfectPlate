<?php

namespace App\Filament\Resources\MenuCategories\Schemas;

use App\Filament\Infolists\HistoryTab;
use App\Models\MenuCategory;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class MenuCategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make([
                    Tabs::make('Category')
                        ->persistTabInQueryString()
                        ->tabs([
                            Tab::make('Details')
                                ->schema([
                                    Section::make('Details')
                                        ->columns(2)
                                        ->schema([
                                            TextEntry::make('name'),
                                            TextEntry::make('slug'),
                                            TextEntry::make('description')
                                                ->placeholder('—')
                                                ->columnSpanFull(),
                                        ]),
                                ]),
                            HistoryTab::make(),
                        ]),
                ])->columnSpan(2),
                Group::make([
                    Section::make('Status')
                        ->schema([
                            IconEntry::make('is_active')
                                ->boolean(),
                            TextEntry::make('sort_order')
                                ->numeric(),
                            TextEntry::make('items')
                                ->label('Items')
                                ->state(fn (MenuCategory $record) => $record->menuItems()->count()),
                        ]),
                ])->columnSpan(1),
            ]);
    }
}
