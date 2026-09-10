<?php

namespace App\Models;

use App\Models\Concerns\CleansUpImageFiles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    use CleansUpImageFiles;

    private const CLEANABLE_IMAGE_FIELDS = ['path'];

    protected $fillable = ['product_id', 'path', 'alt_text', 'is_primary', 'position'];

    protected function casts(): array
    {
        return ['is_primary' => 'boolean'];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getUrlAttribute(): string
    {
        return \Illuminate\Support\Facades\Storage::disk('public')->url($this->path);
    }
}
