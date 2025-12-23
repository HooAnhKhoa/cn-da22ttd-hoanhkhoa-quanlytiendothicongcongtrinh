<?php

namespace Database\Factories;

use App\Models\TaskReview;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskReviewFactory extends Factory
{
    protected $model = TaskReview::class;

    public function definition(): array
    {
        $result = $this->faker->randomElement(['approved', 'rejected', 'needs_revision']);
        $requiresRework = in_array($result, ['rejected', 'needs_revision']);
        
        $data = [
            'task_id' => Task::factory(),
            'reviewer_id' => User::factory(),
            'rating' => $this->faker->optional(0.8)->numberBetween(1, 5),
            'comments' => $this->faker->optional(0.9)->paragraph(),
            'result' => $result,
            'requires_rework' => $requiresRework,
            'is_final' => $result === 'approved' ? true : $this->faker->boolean(30),
            'reviewed_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];

        if ($requiresRework) {
            $data['improvement_suggestions'] = $this->faker->optional(0.7)->paragraph();
            $data['rework_instructions'] = $this->faker->optional(0.5)->paragraph();
            $data['rework_deadline'] = $this->faker->optional(0.4)->dateTimeBetween('+1 week', '+1 month');
        }

        if ($result === 'approved') {
            $data['approved_at'] = $data['reviewed_at'];
        }

        if ($this->faker->boolean(20)) {
            $data['review_files'] = [
                [
                    'name' => $this->faker->word() . '.jpg',
                    'path' => 'reviews/' . $this->faker->uuid() . '.jpg',
                    'type' => 'image/jpeg'
                ]
            ];
        }

        return $data;
    }

    // State methods
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'result' => 'approved',
            'rating' => $this->faker->numberBetween(4, 5),
            'requires_rework' => false,
            'is_final' => true,
            'approved_at' => now(),
            'reviewed_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'result' => 'rejected',
            'rating' => $this->faker->numberBetween(1, 2),
            'requires_rework' => true,
            'is_final' => false,
            'reviewed_at' => now(),
            'improvement_suggestions' => $this->faker->paragraph(),
        ]);
    }

    public function needsRevision(): static
    {
        return $this->state(fn (array $attributes) => [
            'result' => 'needs_revision',
            'rating' => $this->faker->numberBetween(2, 4),
            'requires_rework' => true,
            'is_final' => false,
            'reviewed_at' => now(),
            'improvement_suggestions' => $this->faker->paragraph(),
            'rework_instructions' => $this->faker->paragraph(),
            'rework_deadline' => $this->faker->dateTimeBetween('+1 week', '+2 weeks'),
        ]);
    }
}