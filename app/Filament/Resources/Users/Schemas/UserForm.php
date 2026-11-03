<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('User')
                    ->persistTabInQueryString()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Profile')
                            ->columns(2)
                            ->schema([
                                TextInput::make('name')
                                    ->required(),
                                TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true),
                                TextInput::make('phone')
                                    ->tel(),
                                Textarea::make('address')
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Access')
                            ->columns(2)
                            ->schema([
                                Select::make('role_id')
                                    ->label('Role')
                                    ->relationship('role', 'label')
                                    ->preload()
                                    ->required()
                                    ->disabled(fn (?User $record) => $record?->is(auth()->user()))
                                    ->helperText(fn (?User $record) => $record?->is(auth()->user()) ? 'You can\'t change your own role.' : null),
                                TextInput::make('password')
                                    ->password()
                                    ->revealable()
                                    ->required(fn (string $operation) => $operation === 'create')
                                    ->dehydrated(fn (?string $state) => filled($state))
                                    ->helperText(fn (string $operation) => $operation === 'edit' ? 'Leave blank to keep the current password.' : null),
                            ]),
                        Tab::make('Linked records')
                            ->columns(2)
                            ->visibleOn('edit')
                            ->schema([
                                TextEntry::make('customer_code')
                                    ->label('Customer code')
                                    ->state(fn (User $record) => $record->customer?->customer_code)
                                    ->placeholder('—'),
                                TextEntry::make('employee_code')
                                    ->label('Employee code')
                                    ->state(fn (User $record) => $record->employee?->employee_code)
                                    ->placeholder('—'),
                            ]),
                    ]),
            ]);
    }
}
