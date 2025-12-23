<?php

namespace Database\Factories;

use App\Models\Contract;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContractFactory extends Factory
{
    protected $model = Contract::class;

    public function definition(): array
    {
        $project = Project::inRandomOrder()->first() ?? Project::factory()->create();
        $owner = User::where('user_type', 'owner')->inRandomOrder()->first() ?? User::factory()->create(['user_type' => 'owner']);
        $contractor = User::where('user_type', 'contractor')->inRandomOrder()->first() ?? User::factory()->create(['user_type' => 'contractor']);
        
        $contractValue = $this->faker->numberBetween(1000000, 100000000);
        $advancePercent = $this->faker->numberBetween(10, 30);
        $advancePayment = $contractValue * $advancePercent / 100;

        // Đảm bảo ngày ký và ngày hết hạn logic với nhau
        $signedDate = $this->faker->dateTimeBetween('-1 year', 'now');
        $dueDate = $this->faker->dateTimeBetween($signedDate, '+2 years'); // Luôn sau ngày ký
        
        return [
            'project_id' => $project->id,
            'owner_id' => $owner->id,
            'contractor_id' => $contractor->id,
            'contract_value' => $contractValue,
            'advance_payment' => $advancePayment,
            'signed_date' => $signedDate,
            'due_date' => $dueDate,
            'status' => $this->faker->randomElement(['draft', 'pending_signature', 'active', 'completed']),
            'payment_status' => $this->faker->randomElement(['unpaid', 'partially_paid', 'fully_paid']),
            'total_paid' => $this->faker->numberBetween(0, $contractValue),
            'contract_number' => 'CT-' . $this->faker->unique()->numberBetween(1000, 9999),
            'contract_name' => $this->faker->words(3, true) . ' Contract',
            'description' => $this->faker->paragraph(),
            'contract_file_path' => $this->faker->optional(0.7)->filePath(),
            'contract_file_name' => $this->faker->optional(0.7)->word() . '.pdf',
            'contract_file_size' => $this->faker->optional()->numberBetween(1024, 10485760),
            'contract_file_mime' => $this->faker->optional()->randomElement(['application/pdf', 'application/msword']),
            'additional_files' => $this->faker->optional(0.3)->randomElement([
                [['name' => 'phuluc1.pdf', 'path' => 'contracts/phuluc1.pdf']],
                [['name' => 'bieumau.docx', 'path' => 'contracts/bieumau.docx']]
            ]),
        ];
    }

    // ĐÃ XÓA PHƯƠNG THỨC configure() VÌ GÂY LỖI VỚI CỘT GENERATED

    // State methods
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'payment_status' => 'unpaid',
            'total_paid' => 0,
        ]);
    }

    public function pendingSignature(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending_signature',
            'payment_status' => 'unpaid',
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'payment_status' => $this->faker->randomElement(['unpaid', 'partially_paid']),
            'signed_date' => $this->faker->dateTimeBetween('-3 months', 'now'),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'payment_status' => 'fully_paid',
            // Lưu ý: total_paid sẽ lấy giá trị contract_value có sẵn trong definition
            'total_paid' => $attributes['contract_value'] ?? 1000000, 
        ]);
    }
}