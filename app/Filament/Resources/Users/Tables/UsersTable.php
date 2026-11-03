<?php

namespace App\Filament\Resources\Users\Tables;

use App\Enums\RoleName;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('phone'),
                TextColumn::make('role.label')
                    ->label('Role')
                    ->badge()
                    ->placeholder('No role')
                    ->color(fn (User $record) => match ($record->role?->name) {
                        RoleName::Admin => 'danger',
                        RoleName::Employee => 'info',
                        RoleName::Customer => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('customer.customer_code')
                    ->label('Customer code')
                    ->placeholder('—'),
                TextColumn::make('employee.employee_code')
                    ->label('Employee code')
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->relationship('role', 'label'),
                Filter::make('no_role')
                    ->label('No role')
                    ->query(fn (Builder $query) => $query->whereNull('role_id')),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
