<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'type', 'value', 'min_amount', 'starts_at', 'ends_at',
        'usage_limit', 'used_count', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function isValidFor(int $amount): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $now = Carbon::now();

        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }

        if ($this->ends_at && $now->gt($this->ends_at)) {
            return false;
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return false;
        }

        if ($this->min_amount !== null && $amount < $this->min_amount) {
            return false;
        }

        return true;
    }

    public function discountFor(int $amount): int
    {
        $discount = $this->type === 'percent'
            ? (int) round($amount * $this->value / 100)
            : $this->value;

        return min($discount, $amount);
    }
}
