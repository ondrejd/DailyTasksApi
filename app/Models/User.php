<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * User model.
 * 
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $password
 * 
 * @OA\Schema(
 *     title="User",
 *     description="User description",
 *     required={"name", "email"},
 *     @OA\Property(
 *         format="int64",
 *         property="id",
 *         description="Unique identifier",
 *         example=1,
 *     ),
 *     @OA\Property(
 *         format="string",
 *         property="name",
 *         description="Name of the user",
 *         example="Test User",
 *     ),
 *     @OA\Property(
 *         format="string",
 *         property="email",
 *         description="E-mail address of the user",
 *         example="test@example.com",
 *     )
 * )
 * 
 * TODO Doplnit properties do anotací
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * @return HasMany<Task>
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * @return HasMany<Tag>
     */
    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class);
    }
}
