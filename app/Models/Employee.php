<?php

namespace App\Models;

use App\Enums\RoleName;
use App\Support\CodeGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_code',
        'designation',
        'hire_date',
        'salary',
        'is_active',
    ];

    protected static function booted(): void
    {
        static::creating(function (Employee $employee) {
            $employee->employee_code ??= CodeGenerator::next(static::class, 'employee_code', 'EMP-');
        });

        // Give a new employee's user the employee role, without overriding an existing role.
        static::created(function (Employee $employee) {
            if ($employee->user && $employee->user->role_id === null) {
                $employee->user->update(['role_id' => Role::idFor(RoleName::Employee)]);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'hire_date' => 'date',
            'salary' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderEvents(): BelongsToMany
    {
        return $this->belongsToMany(OrderEvent::class, 'order_event_employee')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'received_by');
    }
}
