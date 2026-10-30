<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class OrderEventMenuItem extends Pivot
{
    public $incrementing = true;

    protected $table = 'order_event_menu_items';

    protected $fillable = [
        'order_event_id',
        'menu_item_id',
        'quantity',
        'unit_price',
        'line_total',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'line_total' => 'decimal:2',
        ];
    }

    public function orderEvent(): BelongsTo
    {
        return $this->belongsTo(OrderEvent::class);
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }
}
