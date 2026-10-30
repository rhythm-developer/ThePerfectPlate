<?php

namespace App\Models;

use App\Enums\OrderEventStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class OrderEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'name',
        'event_date',
        'start_time',
        'end_time',
        'venue',
        'venue_address',
        'guest_count',
        'status',
        'subtotal',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'guest_count' => 'integer',
            'status' => OrderEventStatus::class,
            'subtotal' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function menuItems(): BelongsToMany
    {
        return $this->belongsToMany(MenuItem::class, 'order_event_menu_items')
            ->using(OrderEventMenuItem::class)
            ->withPivot(['quantity', 'unit_price', 'line_total', 'notes'])
            ->withTimestamps();
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'order_event_employee')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Recalculate this event's subtotal from the sum of its menu item lines,
     * then propagate the change up to the parent order.
     */
    public function recalculateSubtotal(): void
    {
        $subtotal = $this->menuItems()->sum('order_event_menu_items.line_total');

        $this->subtotal = $subtotal;
        $this->saveQuietly();

        $this->order->recalculateTotals();
    }
}
