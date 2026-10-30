<?php

namespace App\Filament\Resources\Payments\Schemas;

use App\Enums\PaymentMethod;
use App\Enums\PaymentType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('order_id')
                    ->relationship('order', 'order_number')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->minValue(0),
                Select::make('method')
                    ->options(PaymentMethod::class)
                    ->required(),
                Select::make('type')
                    ->options(PaymentType::class)
                    ->default(PaymentType::Payment)
                    ->required(),
                TextInput::make('reference')
                    ->default(null),
                DateTimePicker::make('paid_at')
                    ->default(now())
                    ->required(),
                Select::make('received_by')
                    ->label('Received by')
                    ->relationship('receivedBy', 'employee_code')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->employee_code} — {$record->user?->name}")
                    ->searchable()
                    ->preload(),
                Textarea::make('notes')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
