<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;

/**
 *
 */
abstract class AbstractFactory extends Factory
{
    /**
     * The current Faker instance.
     *
     * @var \Faker\Generator
     */
    protected $faker2;

    /**
     * Create a new factory instance.
     *
     * @param  int|null  $count
     * @param  \Illuminate\Support\Collection|null  $states
     * @param  \Illuminate\Support\Collection|null  $has
     * @param  \Illuminate\Support\Collection|null  $for
     * @param  \Illuminate\Support\Collection|null  $afterMaking
     * @param  \Illuminate\Support\Collection|null  $afterCreating
     * @param  string|null  $connection
     * @param  \Illuminate\Support\Collection|null  $recycle
     * @return void
     */
    public function __construct($count = null, ?Collection $states = null, ?Collection $has = null, ?Collection $for = null, ?Collection $afterMaking = null, ?Collection $afterCreating = null, $connection = null, ?Collection $recycle = null)
    {
        parent::__construct(
            $count,
            $states,
            $has,
            $for,
            $afterMaking,
            $afterCreating,
            $connection,
            $recycle
        );

        $this->faker2 = currentLocale() === 'ar' ? static::en() : static::ar();
    }

    public static function en()
    {
        return \Faker\Factory::create('en_US');
    }

    public static function ar()
    {
        return \Faker\Factory::create('ar_SA');
    }
}
