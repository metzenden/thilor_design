<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'size', 'color', 'color_hex', 'sku',
        'price_override', 'stock', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getPriceAttribute(): int
    {
        return $this->price_override ?? $this->product->price;
    }

    public function getLabelAttribute(): string
    {
        return collect([$this->size ? "Taille {$this->size}" : null, $this->color])
            ->filter()
            ->implode(' · ');
    }

    public function getInStockAttribute(): bool
    {
        return $this->stock > 0;
    }
}
