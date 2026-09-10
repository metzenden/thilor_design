<?php

namespace App\Models;

use App\Models\Concerns\CleansUpImageFiles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Page extends Model
{
    use CleansUpImageFiles, HasFactory;

    private const CLEANABLE_IMAGE_FIELDS = ['cover_image'];

    protected $fillable = [
        'type', 'title', 'slug', 'excerpt', 'content', 'cover_image',
        'is_published', 'published_at', 'meta_title', 'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
