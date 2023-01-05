<?php

namespace App\Traits;

/**
 * @mixin \App\Models\Abstracts\Model
 */
trait THasBooleanStatus
{
    public static function getAllStatuses(): ?array
    {
        return static::trans("statuses");
    }

    public function getStatusLabelAttribute()
    {
        return static::getStatusLabel($this->status);
    }

    public static function getStatusLabel($status): string
    {
        return static::trans("statuses.{$status}");
    }
}
