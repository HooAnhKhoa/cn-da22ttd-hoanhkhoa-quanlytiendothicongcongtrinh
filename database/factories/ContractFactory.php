<?php

namespace Database\Factories;

use App\Models\Contract;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class ContractFactory extends Factory
{
    protected $model = Contract::class;

    public function definition(): array
    {
        $project = Project::inRandomOrder()->first() ?? Project::factory()->create();
        $contractor = User::where('user_type', 'contractor')->inRandomOrder()->first() ?? User::factory()->contractor()->create();
        
        $now = Carbon::now();
        $projectStart = Carbon::parse($project->start_date);
        
        // Nếu project start_date trong tương lai, điều chỉnh signed_date
        if ($projectStart->greaterThan($now)) {
            // Nếu project bắt đầu trong tương lai, signed_date có thể là hiện tại hoặc quá khứ gần
            $signedDate = $this->faker->dateTimeBetween('-2 months', $now);
        } else {
            // Nếu project đã bắt đầu, signed_date có thể trước hoặc sau start_date
            $signedDate = $this->faker->dateTimeBetween(
                $projectStart->copy()->subMonths(3), // Có thể ký trước 3 tháng
                $projectStart->copy()->addMonths(1)  // Hoặc sau start_date 1 tháng
            );
        }
        
        // due_date phải sau signed_date
        $dueDate = $this->faker->dateTimeBetween(
            $signedDate,
            $signedDate instanceof Carbon ? $signedDate->copy()->addYears(2) : Carbon::parse($signedDate)->addYears(2)
        );
        
        return [
            'project_id' => $project->id,
            'contractor_id' => $contractor->id,
            'contract_value' => $this->faker->randomFloat(2, 50000, 2000000),
            'signed_date' => $signedDate,
            'due_date' => $dueDate,
            'status' => $this->faker->randomElement(['active', 'completed', 'terminated']),
        ];
    }
}