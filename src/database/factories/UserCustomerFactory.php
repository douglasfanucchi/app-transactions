<?php

namespace Database\Factories;

use App\Models\UserCustomer;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserCustomerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserCustomer::class;

    /**
     * Get a new Faker instance
     * @return \Faker\Generator
     */
    public function withFaker()
    {
        return \Faker\Factory::create('pt_BR');
    }

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'type' => 'customer',
            'credits' => $this->faker->randomFloat(2, 0, 1000),
            'document' => $this->faker->cpf,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ];
    }
}
