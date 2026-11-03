<?php

namespace App\Filament\Resources\MenuCategories\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class MenuCategoryForm
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
                            TextInput::make('name')
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (string $operation, $state, callable $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                            TextInput::make('slug')
                                ->required()
                                ->unique(ignoreRecord: true),
                            TextInput::make('sort_order')
                                ->required()
                                ->numeric()
                                ->default(0),
                            Textarea::make('description')
                                ->default(null)
                                ->columnSpanFull(),
                        ]),
                ])->columnSpan(2),
                Group::make([
                    Section::make('Status')
                        ->schema([
                            Toggle::make('is_active')
                                ->default(true)
                                ->required(),
                        ]),
                ])->columnSpan(1),
            ]);
    }
}
