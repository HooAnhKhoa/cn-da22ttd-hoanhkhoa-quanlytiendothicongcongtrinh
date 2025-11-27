<?php

namespace Database\Factories;

use App\Models\Contract;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        $contract = Contract::inRandomOrder()->first() ?? Contract::factory()->create();
        
        $now = Carbon::now();
        $signedDate = Carbon::parse($contract->signed_date);
        
        // Nếu signed_date trong tương lai, điều chỉnh pay_date
        if ($signedDate->greaterThan($now)) {
            // Nếu contract signed_date trong tương lai, pay_date phải là hiện tại hoặc quá khứ
            $payDate = $this->faker->dateTimeBetween('-1 month', $now);
        } else {
            // Nếu contract đã ký, pay_date có thể từ signed_date đến hiện tại
            $payDate = $this->faker->dateTimeBetween($signedDate, $now);
        }
        
        return [
            'contract_id' => $contract->id,
            'amount' => $this->faker->randomFloat(2, 1000, $contract->contract_value / 3),
            'pay_date' => $payDate,
            'method' => $this->faker->randomElement(['bank_transfer', 'credit_card', 'cash', 'check']),
            'note' => $this->faker->optional(0.3)->sentence(),
        ];
    }
}