<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\Factory;

class SiteFactory extends Factory
{
    protected $model = Site::class;

    public function definition(): array
    {
        // Lấy hoặc tạo mới Project để đảm bảo luôn có dữ liệu liên kết
        $project = Project::inRandomOrder()->first() ?? Project::factory()->create();
        
        $startDate = $this->faker->dateTimeBetween($project->start_date, $project->end_date ?? '+3 months');
        
        // Đảm bảo end_date sau start_date và nằm trong phạm vi dự án
        $endDate = $this->faker->dateTimeBetween($startDate, $project->end_date ?? '+1 year');
        
        return [
            'project_id' => $project->id,
            'site_name' => 'Công trường ' . $this->faker->city, // Đổi tên cho chuyên nghiệp hơn
            'description' => $this->faker->paragraph(2),
            'start_date' => $startDate,
            'end_date' => $endDate,
            // Sử dụng randomFloat để khớp với kiểu decimal(5,2) trong database
            'progress_percent' => $this->faker->randomFloat(2, 0, 100),
            // Đồng bộ đầy đủ trạng thái từ Model Site
            'status' => $this->faker->randomElement(['planned', 'in_progress', 'completed', 'on_hold', 'cancelled']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}