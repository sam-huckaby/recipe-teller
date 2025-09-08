<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recipe extends Model
{
    protected $fillable = [
        'user_id',
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
    ];

    protected $casts = [
        'ingredients' => 'array',
        'instructions' => 'array',
        'tags' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'recipe_category');
    }

    public function mealPlans(): HasMany
    {
        return $this->hasMany(MealPlan::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(RecipeVersion::class)->orderBy('version_number', 'desc');
    }

    public function latestVersion(): ?RecipeVersion
    {
        return $this->versions()->first();
    }

    public function createVersionFromCurrentState(): RecipeVersion
    {
        $nextVersionNumber = $this->versions()->max('version_number') + 1;

        return $this->versions()->create([
            'version_number' => $nextVersionNumber,
            'name' => $this->name,
            'url' => $this->url,
            'description' => $this->description,
            'ingredients' => $this->ingredients,
            'instructions' => $this->instructions,
            'prep_time' => $this->prep_time,
            'cook_time' => $this->cook_time,
            'servings' => $this->servings,
            'image_url' => $this->image_url,
            'tags' => $this->tags,
        ]);
    }

    public function getTotalTimeAttribute(): ?int
    {
        if ($this->prep_time && $this->cook_time) {
            return $this->prep_time + $this->cook_time;
        }

        return $this->prep_time ?? $this->cook_time;
    }
}
