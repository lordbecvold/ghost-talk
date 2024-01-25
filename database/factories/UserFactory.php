<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'username' => $this->faker->unique()->userName,
            'password' => bcrypt('password'), // You can customize this as needed
            'token' => Str::random(10),
            'status' => 'active',
        ];
    }
}