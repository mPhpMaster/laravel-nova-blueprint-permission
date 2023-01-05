<?php

namespace Classes;

/**
 * @internal
 */
class Override
{
    /**
     * @param string|null $guard
     *
     * @return string|null
     */
    public static function getModelForGuard(?string $guard = null): ?string
    {
        $guards = collect(config('auth.guards'))
            ->map(function($guard) {
                if( !isset($guard[ 'provider' ]) ) {
                    return null;
                }

                return config("auth.providers.{$guard['provider']}.model");
            });

        return is_null($guard) ? $guards : $guards->get($guard);
    }
}

