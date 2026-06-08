<?php

namespace Database\Factories;

use App\Enums\PaymentStatus;
use App\Models\Client;
use App\Models\Payment;
use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'plan_id' => Plan::factory(),
            'amount' => fake()->randomFloat(2, 29.90, 199.90),
            'due_date' => fake()->dateTimeBetween('-3 months', '+1 month'),
            'paid_at' => null,
            'payment_method' => null,
            'status' => PaymentStatus::PENDING,
            'notes' => null,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PaymentStatus::PAID,
            'paid_at' => fake()->dateTimeBetween('-2 months', 'now'),
            'payment_method' => fake()->randomElement(['credit_card', 'boleto', 'pix']),
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'due_date' => fake()->dateTimeBetween('-2 months', '-1 day'),
            'status' => PaymentStatus::OVERDUE,
        ]);
    }
}
