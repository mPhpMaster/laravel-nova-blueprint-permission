<?php

namespace App\Traits;

use App\Interfaces\IHasPermissionGroup;
use App\Models\Abstracts\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * @mixin \Eloquent
 * @mixin \App\Models\Model
 * @parent \App\Models\Permission
 * @method Builder byName(string|array|\Closure $name)
 */
trait TScopePermission
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string|array|\Closure                 $group
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByGroup(Builder $builder, $group): Builder
    {
        return $builder->whereIn('group', array_flatten(array_wrap(value($group))));
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string|array|\Closure                 $group
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithoutGroup(Builder $builder, ...$group): Builder
    {
	    $group = count($group) === 1 ? head($group) : $group;
	    $group = array_wrap($group);
        return $builder->whereNotIn('group', array_flatten(array_wrap(value($group))));
    }

    /**
     * Get Permissions by prefix, suffix or name.
     *
     * @param string|array $name
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function for(...$name): Builder
    {
        return static::forPermissionQuery([
                                              'startWith' => true,
                                              'endWith' => true,
                                          ], $name);
    }

    /**
     * Get Permissions by prefix name.
     *
     * @param string|array $name
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function forPermission(...$name): Builder
    {
        return static::forPermissionQuery([
                                              'startWith' => true,
                                              'endWith' => false,
                                          ], $name);
    }

    /**
     * Get Permissions by suffix.
     *
     * @param string|array $name
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function forGroups(...$name): Builder
    {
        return static::forPermissionQuery([
                                              'startWith' => false,
                                              'endWith' => true,
                                          ], $name);
    }

    public static function forModel(string|\Illuminate\Database\Eloquent\Model $name): Collection
    {
        try {
            if( $name instanceof IHasPermissionGroup ) {
                $name = $name::getPermissionGroupName();
            } else {
                $name = $name instanceof Model ? getClass($name) : $name;
                $name = class_basename($name);
            }
        } catch(\Exception $exception) {
            return collect();
        }

        return static::query()->where('name', 'like', "%{$name}")->get();
    }

    /**
     * Get Permissions by prefix, suffix or name.
     *
     * @param true[] $options
     * @param mixed  ...$name
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function forPermissionQuery(
        array $options = [
            'startWith' => true,
            'endWith' => true,
        ], ...$name
    ): Builder {
        $valueParser = fn($value) => (toBoolValue($options[ 'endWith' ] ?? false) ? "%" : "") .
            $value .
            (toBoolValue($options[ 'startWith' ] ?? false) ? "%" : "");

        foreach( $name as &$__name ) {
            foreach( $__name as &$_name ) {
                $_name = match (true) {
                    $_name instanceof IHasPermissionGroup => $_name->getPermissionGroupName(),
                    isModel($_name) => class_basename(getClass($_name)),
                    default => $_name
                };
            }
        }

        $query = static::query();
        if( count($name = array_filter(array_flatten($name))) ) {
            if(
                empty(array_filter($name, fn($v) => $v === 'viewAny')) &&
                !empty(array_filter($name, fn($v) => $v === 'view'))
            ) {
                $query = $query->where('name', 'not like', "viewAny%");
            }

            if(
                empty(array_filter($name, fn($v) => $v === 'forceDelete')) &&
                !empty(array_filter($name, fn($v) => $v === 'delete'))
            ) {
                $query = $query->where('name', 'not like', "forceDelete%");
            }

            if(
                empty(array_filter($name, fn($v) => $v === 'forceDestroy')) &&
                !empty(array_filter($name, fn($v) => $v === 'destroy'))
            ) {
                $query = $query->where('name', 'not like', "forceDestroy%");
            }

            $query = $query->where(function($q) use ($name, $valueParser) {
                foreach( $name as $value ) {
                    $q->orWhere('name', 'like', $valueParser($value));
                }
            });
        }

        return $query;
    }
}
