<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class DocumentFactory extends Factory
{
    protected $model = Document::class;

    public function definition(): array
    {
        $project = Project::inRandomOrder()->first() ?? Project::factory()->create();
        $uploader = User::inRandomOrder()->first() ?? User::factory()->create();
        
        // Sử dụng timestamp để đảm bảo an toàn
        $projectStart = Carbon::parse($project->start_date)->getTimestamp();
        $projectEnd = $project->end_date 
            ? Carbon::parse($project->end_date)->getTimestamp()
            : time(); // Sử dụng thời gian hiện tại nếu end_date null
        
        $now = time();
        
        // Nếu project start_date trong tương lai, điều chỉnh lại
        if ($projectStart > $now) {
            $projectStart = $now - (30 * 24 * 60 * 60); // Lùi về 30 ngày trước
        }
        
        // Đảm bảo projectEnd không nhỏ hơn projectStart
        if ($projectEnd < $projectStart) {
            $projectEnd = $projectStart + (30 * 24 * 60 * 60); // Thêm 30 ngày
        }
        
        // Giới hạn projectEnd không vượt quá hiện tại
        if ($projectEnd > $now) {
            $projectEnd = $now;
        }
        
        // Tạo uploaded_at trong khoảng thời gian của project
        $uploadedAtTimestamp = $this->faker->numberBetween($projectStart, $projectEnd);
        $uploadedAt = Carbon::createFromTimestamp($uploadedAtTimestamp);
        
        return [
            'project_id' => $project->id,
            'category' => $this->faker->randomElement(['contract', 'permit', 'report', 'design', 'financial']),
            'document_name' => $this->faker->words(3, true) . ' Document',
            'file_path' => 'documents/' . $this->faker->uuid() . '.pdf',
            'uploaded_by' => $uploader->id,
            'uploaded_at' => $uploadedAt,
        ];
    }
}