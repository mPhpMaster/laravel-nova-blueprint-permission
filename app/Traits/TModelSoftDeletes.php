<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin \App\Models\Abstracts\Model
 * @mixin \Illuminate\Database\Eloquent\SoftDeletes
 * @method static \Illuminate\Database\Eloquent\Model|static firstOrCreateOrRestore(array $attributes, array $values = [])
 */
trait TModelSoftDeletes
{
    public function hasSoftDeletes(object|string|null $class = null): bool
    {
        $class = $class ?? static::class;

        return in_array(
            SoftDeletes::class,
            class_uses_recursive($class)
        );
    }

    /**
     * Get the first record matching the attributes or create it.
     *
     * @param array $attributes
     * @param array $values
     *
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    public function scopeFirstOrCreateOrRestore(Builder $builder, array $attributes = [], array $values = [])
    {
        $query = $this->hasSoftDeletes() ? $this->withTrashed() : $this;

        if( !is_null($instance = $query->where($attributes)->first()) ) {
            if( $instance->trashed() ) {
                $instance->restore();
                $instance->refresh();
            }

            return $instance;
        }

        return tap($this->newModelInstance(array_merge($attributes, $values)), function($instance) {
            $instance->save();
        });
    }
}
