<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        $email = $this->faker->unique()->email;
    	return [
    	    'email' => $email,
            'password' => Hash::make($email), // stores password as email
            'document' =>
                $this->faker
                    ->unique()
                    ->numerify(
                        str_repeat('#', $this->faker->randomElement([11, 14]))
                    ),
            'balance' => pow(
                $this->faker->randomFloat(2, 0, 1000000),
                mt_rand(0, 100) * 0.01 // avoids getting too many absurdly high numbers
            ),
            'type' => $this->faker->randomElement(User::USER_TYPES),
    	];
    }
}
