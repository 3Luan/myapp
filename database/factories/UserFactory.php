<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' =>"Kim Trọng",
            'pic' => $this->faker->imageUrl(),
            'email' =>"tgmail.com",
            'phone' => "328256789",
            'gender' =>"male",
            'role' =>"admin",
            'password' => bcrypt('123'),
        ];
    }
}
