<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    protected static function bootHasSlug(): void
    {
        static::creating(function ($model) {
            if (empty($model->slug) && in_array('slug', $model->getFillable(), true)) {
                $model->slug = static::uniqueSlug($model);
            }
        });
    }

    protected static function uniqueSlug($model): string
    {
        $base = Str::slug($model->title ?? $model->name ?? $model->question ?? '');
        $slug = $base ?: Str::random(8);
        $count = 2;

        while (static::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$count++;
        }

        return $slug;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
