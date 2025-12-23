<?php

namespace Database\Factories;

use App\Models\ContractApproval;
use App\Models\Contract;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContractApprovalFactory extends Factory
{
    protected $model = ContractApproval::class;

    public function definition(): array
    {
        $status = $this->faker->randomElement(['pending', 'approved', 'rejected']);
        
        $dates = [];
        if ($status === 'approved') {
            $dates['approved_at'] = $this->faker->dateTimeBetween('-1 month', 'now');
            $dates['reviewed_at'] = $dates['approved_at'];
        } elseif ($status === 'rejected') {
            $dates['rejected_at'] = $this->faker->dateTimeBetween('-1 month', 'now');
            $dates['reviewed_at'] = $dates['rejected_at'];
        }

        return array_merge([
            'contract_id' => Contract::factory(),
            'approver_id' => User::factory(),
            'status' => $status,
            'comments' => $this->faker->optional(0.7)->paragraph(),
            'approval_file_path' => $this->faker->optional(0.3)->filePath(),
            'approval_file_name' => $this->faker->optional(0.3)->fileName()
        ], $dates);
    }

    // State methods
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'reviewed_at' => null,
            'approved_at' => null,
            'rejected_at' => null,
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'reviewed_at' => now(),
            'approved_at' => now(),
            'rejected_at' => null,
            'comments' => $this->faker->optional()->sentence(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'reviewed_at' => now(),
            'approved_at' => null,
            'rejected_at' => now(),
            'comments' => $this->faker->sentence(),
        ]);
    }
}