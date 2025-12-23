<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\TaskReview;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskReviewSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy các task đã hoàn thành hoặc đang chờ review
        $tasks = Task::whereIn('status', ['completed', 'pending_review'])->get();
        
        // Lấy các owner (người có quyền đánh giá)
        $owners = User::where('user_type', 'owner')->get();
        
        if ($owners->isEmpty() || $tasks->isEmpty()) {
            $this->command->info('Không có task hoặc owner nào để tạo đánh giá.');
            return;
        }

        foreach ($tasks as $task) {
            // Mỗi task có thể có 1-3 lần đánh giá
            $reviewCount = rand(1, 3);
            
            for ($i = 0; $i < $reviewCount; $i++) {
                $result = $this->getReviewResult($i, $reviewCount);
                $isFinal = ($i === $reviewCount - 1 && $result === 'approved');
                
                TaskReview::create([
                    'task_id' => $task->id,
                    'reviewer_id' => $owners->random()->id,
                    'rating' => $this->getRatingForResult($result),
                    'comments' => $this->getRandomComment($result),
                    'improvement_suggestions' => $result !== 'approved' ? $this->getImprovementSuggestion() : null,
                    'result' => $result,
                    'requires_rework' => $result !== 'approved',
                    'rework_instructions' => $result === 'needs_revision' ? $this->getReworkInstructions() : null,
                    'rework_deadline' => $result === 'needs_revision' ? now()->addDays(rand(3, 14)) : null,
                    'is_final' => $isFinal,
                    'reviewed_at' => now()->subDays(rand(0, 30)),
                    'approved_at' => $result === 'approved' ? now()->subDays(rand(0, 30)) : null,
                ]);

                // Nếu là đánh giá cuối cùng và được approved, cập nhật task
                if ($isFinal) {
                    $task->update([
                        'is_approved' => true,
                        'approved_at' => now(),
                        'approved_by' => $owners->random()->id,
                        'status' => 'completed'
                    ]);
                }
            }
        }

        $this->command->info('Đã tạo ' . TaskReview::count() . ' bản ghi đánh giá task.');
    }

    private function getReviewResult(int $iteration, int $total): string
    {
        // Lần đánh giá cuối cùng thường được approved
        if ($iteration === $total - 1) {
            // Đổi $this->faker thành fake()
            return fake()->randomElement(['approved', 'approved', 'approved', 'needs_revision']);
        }
        
        // Đổi $this->faker thành fake()
        return fake()->randomElement(['needs_revision', 'rejected', 'approved']);
    }

    private function getRatingForResult(string $result): ?int
    {
        return match($result) {
            'approved' => rand(4, 5),
            'needs_revision' => rand(2, 4),
            'rejected' => rand(1, 2),
            default => null
        };
    }

    private function getRandomComment(string $result): string
    {
        $comments = [
            'approved' => [
                'Công việc hoàn thành tốt, đạt yêu cầu kỹ thuật.',
                'Chất lượng công việc tốt, có thể nghiệm thu.',
                'Đã hoàn thành đúng tiến độ và yêu cầu.',
                'Công việc được thực hiện chuyên nghiệp.',
                'Đạt yêu cầu, có thể thanh toán.',
            ],
            'needs_revision' => [
                'Cần chỉnh sửa một số chi tiết nhỏ.',
                'Bổ sung thêm tài liệu nghiệm thu.',
                'Chất lượng chưa đồng đều, cần cải thiện.',
                'Thiếu một số hạng mục kiểm tra.',
                'Cần sửa lại phần hoàn thiện.',
            ],
            'rejected' => [
                'Chất lượng không đạt yêu cầu, cần làm lại.',
                'Thiếu nghiêm trọng về an toàn lao động.',
                'Không tuân thủ quy trình kỹ thuật.',
                'Vật liệu sử dụng không đúng chủng loại.',
                'Cần thay đổi phương án thi công.',
            ]
        ];

        return fake()->randomElement($comments[$result] ?? ['Không có nhận xét']);
    }

    private function getImprovementSuggestion(): string
    {
        $suggestions = [
            'Cần kiểm tra kỹ hơn về kích thước thi công.',
            'Bổ sung biên bản nghiệm thu từng hạng mục.',
            'Tăng cường giám sát chất lượng vật liệu.',
            'Cải thiện an toàn lao động tại công trường.',
            'Lập kế hoạch chi tiết hơn cho các công đoạn.',
            'Đào tạo thêm cho nhân công về kỹ thuật.',
            'Sử dụng thiết bị đo đạc chính xác hơn.',
        ];

        return fake()->randomElement($suggestions);
    }

    private function getReworkInstructions(): string
    {
        $instructions = [
            'Tháo dỡ và thi công lại toàn bộ phần này.',
            'Sửa chữa các vết nứt và trám trét lại.',
            'Thay thế vật liệu không đạt chuẩn.',
            'Làm lại phần sơn bả với chất lượng tốt hơn.',
            'Kiểm tra và hiệu chỉnh lại độ cân bằng.',
            'Bổ sung thêm lớp cách nhiệt theo yêu cầu.',
            'Làm lại hệ thống thoát nước cho đúng kỹ thuật.',
        ];

        return fake()->randomElement($instructions);
    }
}