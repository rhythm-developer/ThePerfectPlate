<?php

namespace App\Filament\Resources\Customers\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Customer')
                    ->persistTabInQueryString()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Account')
                            ->columns(2)
                            ->schema([
                                Select::make('user_id')
                                    ->label('User')
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->required(),
                                        TextInput::make('email')
                                            ->email()
                                            ->required()
                                            ->unique('users', 'email'),
                                        TextInput::make('phone'),
                                        TextInput::make('password')
                                            ->password()
                                            ->required()
                                            ->dehydrateStateUsing(fn (string $state) => Hash::make($state)),
                                    ])
                                    ->createOptionUsing(fn (array $data) => User::create($data)->getKey()),
                                TextInput::make('customer_code')
                                    ->placeholder('Auto-generated')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->visibleOn('edit'),
                                Toggle::make('is_active')
                                    ->default(true)
                                    ->required(),
                            ]),
                        Tab::make('Company')
                            ->columns(2)
                            ->schema([
                                TextInput::make('company_name')
                                    ->default(null),
                                Textarea::make('notes')
                                    ->default(null)
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }
}
