<?php

namespace App\Models;

use App\Enums\RoleName;
use App\Support\CodeGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_code',
        'company_name',
        'notes',
        'is_active',
    ];

    protected static function booted(): void
    {
        static::creating(function (Customer $customer) {
            $customer->customer_code ??= CodeGenerator::next(static::class, 'customer_code', 'CUS-');
        });

        // Give a new customer's user the customer role, without overriding an existing role.
        static::created(function (Customer $customer) {
            if ($customer->user && $customer->user->role_id === null) {
                $customer->user->update(['role_id' => Role::idFor(RoleName::Customer)]);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
