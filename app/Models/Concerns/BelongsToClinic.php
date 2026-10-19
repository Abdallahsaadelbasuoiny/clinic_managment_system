<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToClinic
{
    protected static function bootBelongsToClinic(): void
    {
        static::addGlobalScope('clinic', function (Builder $query): void {
            $user = auth()->user();

            if ($user) {
                $query->where(
                    $query->getModel()->qualifyColumn('clinic_id'),
                    $user->clinic_id
                );
            }
        });

        static::creating(function (Model $model): void {
            $user = auth()->user();

            if ($user instanceof User) {
                $model->setAttribute('clinic_id', $user->clinic_id);
            }
        });
    }
}
