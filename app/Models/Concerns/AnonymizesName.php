<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;

trait AnonymizesName
{
    protected static function bootAnonymizesName(): void
    {
        static::creating(function (Model $model): void {
            $model->setAttribute('name', 'name 1');
        });

        static::created(function (Model $model): void {
            $model->setAttribute('name', 'name '.$model->getKey());
            $model->saveQuietly();
        });

        static::updating(function (Model $model): void {
            $model->setAttribute('name', 'name '.$model->getKey());
        });
    }
}
