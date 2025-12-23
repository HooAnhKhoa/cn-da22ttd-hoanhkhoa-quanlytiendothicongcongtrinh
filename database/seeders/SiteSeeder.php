<?php

namespace Database\Seeders;

use App\Models\Site;
use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as FakerFactory;

class SiteSeeder extends Seeder
{
    protected $faker;

    public function __construct()
    {
        $this->faker = FakerFactory::create();
    }

    public function run(): void
    {
        $projects = Project::all();
        
        if ($projects->isEmpty()) {
            $this->command->info('Không có project nào để tạo công trường.');
            return;
        }

        foreach ($projects as $project) {
            // Mỗi project có 1-3 công trường
            $siteCount = rand(1, 3);
            
            for ($i = 0; $i < $siteCount; $i++) {
                Site::factory()->create([
                    'project_id' => $project->id,
                    'status' => $this->getSiteStatusForProject($project),
                ]);
            }
        }

        $this->command->info('Đã tạo ' . Site::count() . ' công trường.');
    }

    private function getSiteStatusForProject($project): string
    {
        return match($project->status) {
            'in_progress' => $this->faker->randomElement(['planned', 'in_progress']),
            'completed' => 'completed',
            'cancelled' => 'cancelled',
            default => 'planned',
        };
    }
}