<?php

namespace App\Filament\Resources\OrderEvents\RelationManagers;

use App\Models\MenuItem;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MenuItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'menuItems';

    protected static ?string $title = 'Menu Items';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('quantity')
                    ->numeric()
                    ->minValue(1)
                    ->required(),
                TextInput::make('unit_price')
                    ->numeric()
                    ->minValue(0)
                    ->required(),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('menuCategory.name')
                    ->label('Category'),
                TextColumn::make('name'),
                TextColumn::make('pivot.quantity')
                    ->label('Qty')
                    ->numeric(),
                TextColumn::make('pivot.unit_price')
                    ->label('Unit price')
                    ->money(),
                TextColumn::make('pivot.line_total')
                    ->label('Line total')
                    ->money(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                    ->recordSelect(fn (Select $select) => $select->searchable()->preload()->live())
                    ->schema(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        TextInput::make('quantity')
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->required(),
                        TextInput::make('unit_price')
                            ->numeric()
                            ->minValue(0)
                            ->default(fn (Get $get) => MenuItem::find($get('recordId'))?->price)
                            ->required(),
                        Textarea::make('notes')
                            ->columnSpanFull(),
                    ])
                    ->mutateFormDataUsing(fn (array $data) => [
                        ...$data,
                        'line_total' => round(($data['quantity'] ?? 0) * ($data['unit_price'] ?? 0), 2),
                    ])
                    ->after(fn ($livewire) => $livewire->getOwnerRecord()->recalculateSubtotal()),
            ])
            ->recordActions([
                EditAction::make()
                    ->mutateFormDataUsing(fn (array $data) => [
                        ...$data,
                        'line_total' => round(($data['quantity'] ?? 0) * ($data['unit_price'] ?? 0), 2),
                    ])
                    ->after(fn ($livewire) => $livewire->getOwnerRecord()->recalculateSubtotal()),
                DetachAction::make()
                    ->after(fn ($livewire) => $livewire->getOwnerRecord()->recalculateSubtotal()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make()
                        ->after(fn ($livewire) => $livewire->getOwnerRecord()->recalculateSubtotal()),
                ]),
            ]);
    }
}
