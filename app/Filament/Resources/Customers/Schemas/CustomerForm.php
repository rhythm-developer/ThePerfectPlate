<?php

namespace App\Filament\Resources\Customers\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make([
                    Section::make('Account')
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
                        ]),
                    Section::make('Company')
                        ->schema([
                            TextInput::make('company_name')
                                ->default(null),
                            Textarea::make('notes')
                                ->default(null)
                                ->columnSpanFull(),
                        ]),
                ])->columnSpan(2),
                Group::make([
                    Section::make('Status')
                        ->schema([
                            TextInput::make('customer_code')
                                ->placeholder('Auto-generated')
                                ->disabled()
                                ->dehydrated(false)
                                ->visibleOn('edit'),
                            Toggle::make('is_active')
                                ->default(true)
                                ->required(),
                        ]),
                ])->columnSpan(1),
            ]);
    }
}
