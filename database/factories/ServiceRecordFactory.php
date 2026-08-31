<?php

namespace Database\Factories;

use App\Enums\ServiceType;
use App\Models\ServiceRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceRecord>
 */
class ServiceRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'service_type' => fake()->randomElement(ServiceType::cases())->value,
            'description'  => fake()->sentence(),
            'service_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'cost'         => fake()->randomFloat(2, 50, 2000),
        ];
    }
}
