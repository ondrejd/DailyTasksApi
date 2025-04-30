<?php

namespace App\Http\Requests;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @method ?User user()
 * @method mixed route()
 */
class TagUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var User $user */
        $user = $this->user();
        /** @var Tag $tag */
        $tag = $this->route('tag');

        if ($tag->user_id !== $user->id) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var User $user */
        $user = $this->user();

        return [
            'name' => [
                'string',
                Rule::unique('tags', 'name')->where('user_id', $user->id),
            ],
            'color' => 'string|max:7',
        ];
    }
}
