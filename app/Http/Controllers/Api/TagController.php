<?php

namespace App\Http\Controllers\Api;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TagController extends BaseController
{
    /**
     * @OA\Get(
     *      path="/api/tag",
     *      summary="List tags",
     *      description="Listing of all tags for current user",
     *      tags={"Tags"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(response="200", description="Listed tags"),
     *      @OA\Response(response="400", description="Unauthorised")
     * )
     */
    public function list(Request $request)
    {
        return $this->sendResponse(
            $request->user()->tags->map(fn (Tag $tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
                'color' => $tag->color,
            ])->toArray()
        );
    }

    /**
     * @OA\Post(
     *      path="/api/tag",
     *      summary="Create new tag",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="name",
     *                      description="Name of the new tag",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="email",
     *                      description="Color of the new tag in hex format",
     *                      type="string"
     *                  ),
     *                  required={"name"},
     *                  example={"name": "Personal", "color": "#333333"}
     *              )
     *          )
     *      ),
     *      tags={"Tags"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(response="200", description="Tag created successfully"),
     *      @OA\Response(response="400", description="Unauthorised")
     * )
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'color' => 'required|string|max:7',
        ]);

        $tag = new Tag($validator->validated());
        $request->user->tags()->attach($tag);
    }

    /**
     * @OA\Put(
     *      path="/api/tag/{id}",
     *      summary="Update selected tag",
     *      tags={"Tags"},
     *      @OA\Parameter(
     *          description="ID of the tag",
     *          in="path",
     *          name="id",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(response="200", description="Tag updated successfully"),
     *      @OA\Response(response="400", description="Unauthorised")
     * )
     */
    public function update(Request $request, Tag $tag)
    {
        //
    }

    /**
     * @OA\Delete(
     *      path="/api/tag/{id}",
     *      summary="Delete selected tag",
     *      tags={"Tags"},
     *      @OA\Parameter(
     *          description="ID of the tag",
     *          in="path",
     *          name="id",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(response="200", description="Tag deleted successfully"),
     *      @OA\Response(response="400", description="Unauthorised")
     * )
     */
    public function delete(Tag $tag)
    {
        //
    }
}
