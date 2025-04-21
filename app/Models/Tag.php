<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Tag model.
 * 
 * @property integer $id
 * @property string $name
 * @property ?string $color
 * @property integer $user_id
 * @property User $user
 * @property-read Collection<Task> $tasks
 * 
 * @OA\Schema(
 *     title="Tag",
 *     description="Single task's tag",
 *     required={"name"},
 *     @OA\Property(
 *         format="int64",
 *         property="id",
 *         description="Unique identifier",
 *         example=1,
 *     ),
 *     @OA\Property(
 *         format="string",
 *         property="name",
 *         description="Name of the tag",
 *         example="Personal",
 *     ),
 *     @OA\Property(
 *         format="string",
 *         property="color",
 *         description="Color of the tag in hex format",
 *         example="#0066aa",
 *     )
 * )
 */
class Tag extends Model
{
    /** @use HasFactory<\Database\Factories\TagFactory> */
    use HasFactory;

    /**
     * @var string|null
     */
    protected $table = 'tags';
    
    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'color',
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
        return [];
    }

    /**
     * @return BelongsTo<User>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsToMany<Task>
     */
    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class);
    }
}
