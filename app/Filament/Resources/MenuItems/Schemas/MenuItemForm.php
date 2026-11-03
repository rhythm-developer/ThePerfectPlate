<?php

namespace App\Filament\Resources\MenuItems\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MenuItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make([
                    Section::make('Details')
                        ->columns(2)
                        ->schema([
                            Select::make('menu_category_id')
                                ->relationship('menuCategory', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            TextInput::make('name')
                                ->required(),
                            TextInput::make('price')
                                ->required()
                                ->numeric()
                                ->minValue(0),
                            Textarea::make('description')
                                ->default(null)
                                ->columnSpanFull(),
                        ]),
                    Section::make('Image')
                        ->schema([
                            FileUpload::make('image')
                                ->image()
                                ->directory('menu-items'),
                        ]),
                ])->columnSpan(2),
                Group::make([
                    Section::make('Status')
                        ->schema([
                            Toggle::make('is_available')
                                ->default(true),
                            Toggle::make('is_vegetarian')
                                ->default(false),
                        ]),
                ])->columnSpan(1),
            ]);
    }
}
