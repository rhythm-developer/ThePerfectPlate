<?php

namespace App\Filament\Resources\Employees\Schemas;

use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Employee')
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
                                TextInput::make('employee_code')
                                    ->placeholder('Auto-generated')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->visibleOn('edit'),
                                Toggle::make('is_active')
                                    ->default(true)
                                    ->required(),
                            ]),
                        Tab::make('Employment')
                            ->columns(2)
                            ->schema([
                                TextInput::make('designation')
                                    ->required(),
                                DatePicker::make('hire_date'),
                                TextInput::make('salary')
                                    ->numeric()
                                    ->default(null),
                            ]),
                    ]),
            ]);
    }
}
