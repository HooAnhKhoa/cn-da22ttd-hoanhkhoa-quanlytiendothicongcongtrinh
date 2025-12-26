<?php

namespace Database\Factories;

use App\Models\Contract;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContractFactory extends Factory
{
    protected $model = Contract::class;

    public function definition(): array
    {
        $project = Project::inRandomOrder()->first() ?? Project::factory()->create();
        
        $contractValue = $this->faker->numberBetween(1000000, 100000000);
        $advancePercent = $this->faker->numberBetween(10, 30);
        $advancePayment = $contractValue * $advancePercent / 100;

        $signedDate = $this->faker->dateTimeBetween('-1 year', 'now');
        $dueDate = $this->faker->dateTimeBetween($signedDate, '+2 years');
        
        // Tạo file bổ sung mẫu
        $sampleFiles = [
            ['name' => 'phuluc1.pdf', 'path' => 'contracts/phuluc1.pdf'],
            ['name' => 'bieumau.docx', 'path' => 'contracts/bieumau.docx']
        ];

        return [
            'project_id' => $project->id,
            'contract_value' => $contractValue,
            'advance_payment' => $advancePayment,
            'signed_date' => $signedDate,
            'due_date' => $dueDate,
            'status' => $this->faker->randomElement(['draft', 'pending_signature', 'active', 'completed', 'terminated']),
            'contract_number' => 'CT-' . $this->faker->unique()->numberBetween(1000, 9999),
            'contract_name' => $this->faker->words(3, true) . ' Contract',
            'description' => $this->faker->paragraph(),
            'contract_file_path' => $this->faker->optional(0.7)->filePath(),
            'contract_file_name' => $this->faker->optional(0.7)->word() . '.pdf',
            
            // SỬA LỖI Ở ĐÂY: Dùng boolean() để check thay vì optional()->json()
            'additional_files' => $this->faker->boolean(30) ? $sampleFiles : null,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'signed_date' => $this->faker->dateTimeBetween('-3 months', 'now'),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }
}