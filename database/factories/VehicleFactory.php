<?php

namespace Database\Factories;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $brands = [
            'Fiat'       => ['Uno', 'Palio', 'Argo', 'Mobi', 'Toro'],
            'Volkswagen' => ['Gol', 'Polo', 'Virtus', 'Nivus', 'T-Cross'],
            'Chevrolet'  => ['Onix', 'Prisma', 'Tracker', 'S10', 'Cruze'],
            'Ford'       => ['Ka', 'Fiesta', 'EcoSport', 'Ranger'],
            'Toyota'     => ['Corolla', 'Etios', 'Hilux', 'Yaris'],
            'Honda'      => ['Civic', 'Fit', 'HR-V', 'City'],
            'Hyundai'    => ['HB20', 'Creta', 'Tucson'],
            'Renault'    => ['Kwid', 'Sandero', 'Duster', 'Logan'],
        ];

        $brand = fake()->randomElement(array_keys($brands));
        $model = fake()->randomElement($brands[$brand]);

        return [
            'brand' => $brand,
            'model' => $model,
            'year'  => fake()->numberBetween(2010, 2026),
            'plate' => strtoupper(fake()->unique()->bothify('???#?##')),
        ];
    }
}
