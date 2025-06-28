<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\User;
use App\Models\Department;
use App\Models\DocumentType;
use App\Models\DocumentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Document::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sentAt = $this->faker->optional(0.3)->dateTimeBetween('-1 year', 'now');
        
        return [
            'tracking_number' => $this->faker->unique()->regexify('[A-Z]{3}[0-9]{6}'),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'department_id' => Department::factory(),
            'document_type_id' => DocumentType::factory(),
            'status_id' => DocumentStatus::factory(),
            'direction' => $sentAt ? 'outgoing' : 'incoming',
            'source' => $this->faker->company(),
            'received_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'sent_at' => $sentAt,
            'date_received' => $this->faker->date(),
            'due_date' => $this->faker->optional()->dateTimeBetween('now', '+1 month'),
            'created_by' => User::factory(),
            'current_assignee' => User::factory(),
            'file_path' => $this->faker->optional()->filePath(),
            'qr_code_path' => $this->faker->optional()->filePath(),
            'metadata' => ['source' => $this->faker->company()],
        ];
    }
} 