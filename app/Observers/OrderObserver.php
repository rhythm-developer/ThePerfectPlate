<?php

namespace App\Observers;

use App\Models\Order;

class OrderObserver
{
    public function saving(Order $order): void
    {
        $order->total = round($order->subtotal - $order->discount + $order->tax, 2);
    }
}
