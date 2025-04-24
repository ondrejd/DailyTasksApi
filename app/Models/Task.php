<?php

namespace App\Models;

use App\Enums\TaskStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Task model.
 * 
 * @property integer $id
 * @property string $name
 * @property Carbon|\DateTime|string $targeted_at
 * @property Carbon|\DateTime|string|null $fulfilled_at
 * @property TaskStatusEnum|string $status
 * @property integer $user_id
 * @property Carbon|\DateTime|string $created_at
 * @property Carbon|\DateTime|string|null $updated_at
 * @property User $user
 * @property-read Collection<Tag> $tags
 * 
 * @OA\Schema(
 *     title="Task",
 *     description="Single task",
 *     required={"name","targeted_at"},
 *     @OA\Property(
 *         type="integer",
 *         format="int64",
 *         property="id",
 *         description="Unique identifier",
 *         example=1,
 *     ),
 *     @OA\Property(
 *         type="string",
 *         property="name",
 *         description="Name of the task",
 *         example="Personal",
 *     ),
 *     @OA\Property(
 *         type="string",
 *         format="date-time",
 *         property="targeted_at",
 *         description="Date-time to when is task targeted",
 *     ),
 *     @OA\Property(
 *         type="string",
 *         format="date-time",
 *         nullable=true,
 *         property="fulfilled_at",
 *         description="Date-time to when was task fulfilled",
 *     ),
 *     @OA\Property(
 *         type="string",
 *         property="status",
 *         description="Current status of the task (`ongoing`, `finished`, `postponed`, `canceled`)",
 *         example="ongoing",
 *     ),
 *     @OA\Property(
 *         type="array",
 *         property="tags",
 *         description="Tags attached to the task.",
 *         @OA\Items(ref="#/components/schemas/Tag"),
 *     )
 * )
 */
class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;

    /**
     * @var string|null
     */
    protected $table = 'tasks';
    
    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'targeted_at',
        'fulfilled_at',
        'status',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [];

    /**
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'targeted_at' => 'datetime',
            'fulfilled_at' => 'datetime',
            'status' => TaskStatusEnum::class,
        ];
    }

    /**
     * @return BelongsTo<User>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsToMany<Tag>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }
}
