<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecipeVersion extends Model
{
    protected $fillable = [
        'recipe_id',
        'version_number',
        'name',
        'url',
        'description',
        'ingredients',
        'instructions',
        'prep_time',
        'cook_time',
        'servings',
        'image_url',
        'tags',
        'categories',
    ];

    protected $casts = [
        'ingredients' => 'array',
        'instructions' => 'array',
        'tags' => 'array',
        'categories' => 'array',
    ];

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function getTotalTimeAttribute(): ?int
    {
        if ($this->prep_time && $this->cook_time) {
            return $this->prep_time + $this->cook_time;
        }

        return $this->prep_time ?? $this->cook_time;
    }

    public function getFormattedCreatedAtAttribute(): string
    {
        return $this->created_at->format('M j, Y \a\t g:i A');
    }
}
