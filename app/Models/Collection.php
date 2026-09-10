<?php

namespace App\Models;

use App\Models\Concerns\CleansUpImageFiles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Collection extends Model
{
    use CleansUpImageFiles, HasFactory;

    private const CLEANABLE_IMAGE_FIELDS = ['image'];

    protected $table = 'collections';

    protected $fillable = [
        'name', 'slug', 'description', 'image', 'position',
        'is_active', 'meta_title', 'meta_description',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'collection_product');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? \Illuminate\Support\Facades\Storage::disk('public')->url($this->image) : null;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
