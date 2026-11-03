<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class OrderEventEmployee extends Pivot
{
    public $incrementing = true;

    protected $table = 'order_event_employee';

    protected $fillable = [
        'order_event_id',
        'employee_id',
        'role',
    ];

    public function orderEvent(): BelongsTo
    {
        return $this->belongsTo(OrderEvent::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
