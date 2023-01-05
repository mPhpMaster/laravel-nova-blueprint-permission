<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * @mixin \Eloquent
 * @method Builder byName(string|array|\Closure $name)
 */
trait THasScopeName
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string|array|\Closure                 $name
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByName(Builder $builder, $name): Builder
    {
        return $builder->whereIn('name', array_wrap(value($name)));
    }
}
