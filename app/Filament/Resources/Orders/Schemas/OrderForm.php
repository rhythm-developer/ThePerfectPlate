<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Enums\OrderStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make([
                    Section::make('Order')
                        ->schema([
                            Select::make('customer_id')
                                ->relationship('customer', 'customer_code')
                                ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->customer_code} — {$record->user?->name}")
                                ->searchable(['customer_code'])
                                ->preload()
                                ->required(),
                            Textarea::make('notes')
                                ->default(null)
                                ->columnSpanFull(),
                        ]),
                ])->columnSpan(2),
                Group::make([
                    Section::make('Status')
                        ->schema([
                            TextInput::make('order_number')
                                ->placeholder('Auto-generated')
                                ->disabled()
                                ->dehydrated(false)
                                ->visibleOn('edit'),
                            Select::make('status')
                                ->options(OrderStatus::class)
                                ->default(OrderStatus::Pending)
                                ->required(),
                        ]),
                    Section::make('Totals')
                        ->schema([
                            TextInput::make('subtotal')
                                ->numeric()
                                ->default(0.0)
                                ->disabled()
                                ->dehydrated()
                                ->helperText('Calculated from event menu items.'),
                            TextInput::make('discount')
                                ->required()
                                ->numeric()
                                ->default(0.0),
                            TextInput::make('tax')
                                ->required()
                                ->numeric()
                                ->default(0.0),
                            TextInput::make('total')
                                ->numeric()
                                ->default(0.0)
                                ->disabled()
                                ->dehydrated()
                                ->helperText('Subtotal − discount + tax.'),
                        ]),
                ])->columnSpan(1),
            ]);
    }
}
