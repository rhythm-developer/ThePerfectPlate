<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Enums\OrderStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('order_number')
                    ->required()
                    ->default(fn () => 'ORD-'.now()->format('Y').'-'.mb_strtoupper(Str::random(6)))
                    ->unique(ignoreRecord: true),
                Select::make('customer_id')
                    ->relationship('customer', 'customer_code')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->customer_code} — {$record->user?->name}")
                    ->searchable(['customer_code'])
                    ->preload()
                    ->required(),
                Select::make('status')
                    ->options(OrderStatus::class)
                    ->default(OrderStatus::Pending)
                    ->required(),
                TextInput::make('discount')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('tax')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('subtotal')
                    ->numeric()
                    ->default(0.0)
                    ->disabled()
                    ->dehydrated()
                    ->helperText('Calculated from event menu items.'),
                TextInput::make('total')
                    ->numeric()
                    ->default(0.0)
                    ->disabled()
                    ->dehydrated()
                    ->helperText('Subtotal − discount + tax.'),
                Textarea::make('notes')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
