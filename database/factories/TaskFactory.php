<?php

namespace Database\Factories;

use App\Enums\TaskStatusEnum;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * @var class-string<\Illuminate\Database\Eloquent\Model|Task>
     */
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $targetedAt = fake()->dateTimeInInterval('-1 month', '1 month');
        $status = fake()->randomElement(TaskStatusEnum::cases())->value;
        $fulfilledAt = null;

        if ($status === TaskStatusEnum::FINISHED->value) {
            $fulfilledAt = $targetedAt;
        }

        return [
            'user_id' => User::all()->random(),
            'name' => fake()->sentence,
            'targeted_at' => $targetedAt,
            'fulfilled_at' => $fulfilledAt,
            'status' => $status,
        ];
    }
}
