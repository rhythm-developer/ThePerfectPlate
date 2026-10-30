<?php

namespace App\Observers;

use App\Models\OrderEventMenuItem;

class OrderEventMenuItemObserver
{
    public function saving(OrderEventMenuItem $pivot): void
    {
        $pivot->line_total = round($pivot->quantity * $pivot->unit_price, 2);
    }

    public function saved(OrderEventMenuItem $pivot): void
    {
        $pivot->orderEvent?->recalculateSubtotal();
    }

    public function deleted(OrderEventMenuItem $pivot): void
    {
        $pivot->orderEvent?->recalculateSubtotal();
    }
}
