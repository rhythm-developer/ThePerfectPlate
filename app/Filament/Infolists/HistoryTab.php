<?php

namespace App\Filament\Infolists;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Tabs\Tab;

/**
 * The "History" tab shared by every resource's view page.
 */
class HistoryTab
{
    public static function make(bool $withDeleted = false): Tab
    {
        return Tab::make('History')
            ->icon('heroicon-o-clock')
            ->columns(2)
            ->schema([
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->placeholder('—')
                    ->visible($withDeleted),
            ]);
    }
}
