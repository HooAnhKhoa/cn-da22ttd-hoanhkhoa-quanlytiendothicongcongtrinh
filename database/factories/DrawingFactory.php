<?php

namespace Database\Factories;

use App\Models\Drawing;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class DrawingFactory extends Factory
{
    protected $model = Drawing::class;

    public function definition(): array
    {
        $project = Project::inRandomOrder()->first() ?? Project::factory()->create();
        $approver = User::where('user_type', 'engineer')->inRandomOrder()->first() ?? User::factory()->engineer()->create();
        
        $projectStart = Carbon::parse($project->start_date);
        $now = Carbon::now();
        
        // Nếu project start_date trong tương lai, điều chỉnh về quá khứ
        if ($projectStart->greaterThan($now)) {
            $projectStart = $now->copy()->subMonths(2); // Lùi về 2 tháng trước
        }
        
        $projectEnd = $project->end_date ? Carbon::parse($project->end_date) : $now;
        
        // Đảm bảo projectEnd không vượt quá hiện tại
        if ($projectEnd->greaterThan($now)) {
            $projectEnd = $now;
        }
        
        // Đảm bảo projectEnd sau projectStart
        if ($projectEnd->lessThanOrEqualTo($projectStart)) {
            $projectEnd = $projectStart->copy()->addDays(1);
        }
        
        // approved_at có thể null (chưa được approve)
        $approvedAt = $this->faker->optional(0.8)->dateTimeBetween($projectStart, $projectEnd);
        
        return [
            'project_id' => $project->id,
            'code' => $this->faker->bothify('DWG-#####'),
            'version' => $this->faker->randomElement(['1.0', '1.1', '2.0', '2.1']),
            'file_path' => 'drawings/' . $this->faker->uuid() . '.dwg',
            'approved_by' => $approver->id,
            'approved_at' => $approvedAt,
        ];
    }
}