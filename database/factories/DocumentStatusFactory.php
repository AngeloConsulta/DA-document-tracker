<?php

namespace Database\Factories;

use App\Models\DocumentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DocumentStatus>
 */
class DocumentStatusFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DocumentStatus::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(2, true),
            'code' => $this->faker->unique()->regexify('[A-Z]{2}'),
            'description' => $this->faker->sentence(),
            'is_active' => true,
        ];
    }
} 