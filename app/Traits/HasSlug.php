<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;
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

        static::updating(function ($model) {
            if (! in_array('slug', $model->getFillable(), true)) {
                return;
            }

            $incoming = $model->slug;

            if ($incoming === null || trim((string) $incoming) === '') {
                $model->slug = $model->getOriginal('slug');

                if (empty($model->slug)) {
                    $model->slug = static::uniqueSlug($model);
                }
            }
        });
    }

    protected static function uniqueSlug($model): string
    {
        $base = Str::slug($model->title ?? $model->name ?? $model->question ?? '');
        $slug = $base ?: Str::random(8);
        $count = 2;

        while (static::slugExists($slug)) {
            $slug = $base.'-'.$count++;
        }

        return $slug;
    }

    protected static function slugExists(string $slug): bool
    {
        $query = static::where('slug', $slug);

        if (in_array(SoftDeletes::class, class_uses_recursive(static::class), true)) {
            $query->withTrashed();
        }

        return $query->exists();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}