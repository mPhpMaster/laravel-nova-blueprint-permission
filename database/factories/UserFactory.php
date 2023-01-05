<?php

namespace Database\Factories;

use App\Interfaces\IUserType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model|\Model>
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->firstName(),
            'email' => $email = fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => \Hash::make($email),
            'remember_token' => Str::random(10),
            // 'user_type' => fake()->randomElement([ IUserType::NORMAL, IUserType::SUB_USER, IUserType::MERCHANT, IUserType::MERCHANT_SUB_USER ]),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * @return static
     */
    // public function normal()
    // {
    //     return $this->state(fn(array $attributes) => [ 'user_type' => IUserType::NORMAL ]);
    // }

    /**
     * @return static
     */
    // public function subUser()
    // {
    //     return $this->state(fn(array $attributes) => [ 'user_type' => IUserType::SUB_USER ]);
    // }

    /**
     * @return static
     */
    // public function merchant()
    // {
    //     return $this->state(fn(array $attributes) => [ 'user_type' => IUserType::MERCHANT ]);
    // }

    /**
     * @return static
     */
    // public function merchantSubUser()
    // {
    //     return $this->state(fn(array $attributes) => [ 'user_type' => IUserType::MERCHANT_SUB_USER ]);
    // }
}
