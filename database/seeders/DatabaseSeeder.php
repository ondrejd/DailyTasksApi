<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    private array $users = [
        [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ],
        [
            'name' => 'Tester User',
            'email' => 'tester@example.com',
        ],
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach ($this->users as $userData) {
            $user = User::factory()->create($userData);

            $tags = Tag::factory(5)->create([
                'user_id' => $user->id,
            ]);
    
            $tasks = Task::factory(25)->create([
                'user_id' => $user->id,
            ]);
    
            $tasks->each(fn (Task $task) => $task->tags()->sync(
                $tags->random(random_int(1, 5))->pluck('id')->toArray()
            ));
        }
    }
}
