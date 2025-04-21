<?php

namespace App\Http\Controllers\Api;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * @OA\Tag(
 *     name="Tags",
 *     description="Tasks tags API",
 * )
 */
class TagController extends BaseController
{
    /**
     * @OA\Get(
     *      path="/api/tag",
     *      summary="List tags",
     *      description="Listing of all tags for the current user",
     *      tags={"Tags"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response="200",
     *          description="Listed tags",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="success",
     *                      type="bool",
     *                      example=true,
     *                  ),
     *                  @OA\Property(
     *                      property="data",
     *                      type="array",
     *                      @OA\Items(ref="#/components/schemas/Tag")
     *                  ),
     *                  @OA\Property(
     *                      property="message",
     *                      type="string",
     *                      example="",
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response="401", ref="#/components/responses/Unauthenticated"),
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
     *      description="Create new tag for the current user",
     *      tags={"Tags"},
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          request="Tag",
     *          description="Tag object that needs to be added to the store",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      format="string",
     *                      property="name",
     *                      description="Name of the tag",
     *                      example="Personal",
     *                  ),
     *                  @OA\Property(
     *                      format="string",
     *                      property="color",
     *                      description="Color of the tag in hex format",
     *                      example="#0066aa",
     *                  ),
     *                  required={"name"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Tag created successfully",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="success",
     *                      type="bool",
     *                      example=true,
     *                  ),
     *                  @OA\Property(property="data", ref="#/components/schemas/Tag"),
     *                  @OA\Property(
     *                      property="message",
     *                      type="string",
     *                      example="Tag created successfully",
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response="401", ref="#/components/responses/Unauthenticated"),
     *      @OA\Response(
     *          response="422",
     *          description="Validation error",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="success",
     *                      type="bool",
     *                      example=false,
     *                  ),
     *                  @OA\Property(
     *                      property="data",
     *                      description="Contains array with keys of fields name (`name`, `color`) with an array of errors which was found",
     *                      type="object",
     *                      @OA\Property(
     *                          property="name",
     *                          description="Name of field where the validation errors occured",
     *                          type="array",
     *                          @OA\Items(type="string", example="The name has already been taken"),
     *                      ),
     *                  ),
     *                  @OA\Property(
     *                      property="message",
     *                      type="string",
     *                      example="Validation error",
     *                  ),
     *              ),
     *          ),
     *      ),
     * )
     */
    public function create(Request $request)
    {
        // TODO Make normal request class (and validator is the same as in update method...)
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                Rule::unique('tags', 'name')->where('user_id', $request->user()->id),
            ],
            'color' => 'string|max:7',
        ]);

        $tag = new Tag($validator->validated());
        $request->user()->tags()->save($tag);

        // TODO Make normal response class with OpenApi annotations...
        return $this->sendResponse(
            [
                'id' => $tag->id,
                'name' => $tag->name,
                'color' => $tag->color,
            ],
            __('Tag created successfully')
        );
    }

    /**
     * @OA\Put(
     *      path="/api/tag/{id}",
     *      summary="Update selected tag",
     *      description="Create selected tag of the current user",
     *      tags={"Tags"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          description="ID of the tag",
     *          in="path",
     *          name="id",
     *          required=true,
     *          example=1,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\RequestBody(
     *          request="Tag",
     *          description="Tag object that needs to be updated",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      format="string",
     *                      property="name",
     *                      description="Name of the tag",
     *                      example="Other",
     *                  ),
     *                  @OA\Property(
     *                      format="string",
     *                      property="color",
     *                      description="Color of the tag in hex format",
     *                      example="#a66666",
     *                  ),
     *                  required={"name"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Tag updated successfully",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="success",
     *                      type="bool",
     *                      example=true,
     *                  ),
     *                  @OA\Property(property="data", ref="#/components/schemas/Tag"),
     *                  @OA\Property(
     *                      property="message",
     *                      type="string",
     *                      example="Tag updated successfully",
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response="400", description="User is unauthorized to update this tag"),
     *      @OA\Response(response="401", ref="#/components/responses/Unauthenticated"),
     *      @OA\Response(response="404", ref="#/components/responses/NotFound"),
     *      @OA\Response(
     *          response="422",
     *          description="Validation error",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="success",
     *                      type="bool",
     *                      example=false,
     *                  ),
     *                  @OA\Property(
     *                      property="data",
     *                      description="Contains array with keys of fields name (`name`, `color`) with an array of errors which was found",
     *                      type="object",
     *                      @OA\Property(
     *                          property="name",
     *                          description="Name of field where the validation errors occured",
     *                          type="array",
     *                          @OA\Items(type="string", example="The name has already been taken"),
     *                      ),
     *                  ),
     *                  @OA\Property(
     *                      property="message",
     *                      type="string",
     *                      example="Validation error",
     *                  ),
     *              ),
     *          ),
     *      ),
     * )
     */
    public function update(Request $request, Tag $tag)
    {
        $user = $request->user();

        if ($tag->user_id !== $user->id) {
            return $this->sendError(__('Unauthorized'), [], 400);
        }

        // TODO Make normal request class (and validator is the same as in create method...)
        $validator = Validator::make($request->all(), [
            'name' => [
                'string',
                Rule::unique('tags', 'name')->where('user_id', $request->user()->id),
            ],
            'color' => 'string|max:7',
        ]);

        $values = $validator->validated();

        if ($values['name']) {
            $tag->name = $values['name'];
        }

        if ($values['color']) {
            $tag->color = $values['color'];
        }

        $tag->save();

        // TODO Make normal response class with OpenApi annotations...
        return $this->sendResponse(
            [
                'id' => $tag->id,
                'name' => $tag->name,
                'color' => $tag->color,
            ],
            __('Tag updated successfully')
        );
    }

    /**
     * @OA\Delete(
     *      path="/api/tag/{id}",
     *      summary="Delete selected tag",
     *      description="Create selected tag of the current user",
     *      tags={"Tags"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          description="ID of the tag",
     *          in="path",
     *          name="id",
     *          required=true,
     *          example=1,
     *          @OA\Schema(type="string"),
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Tag deleted successfully",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="success",
     *                      type="bool",
     *                      example=true,
     *                  ),
     *                  @OA\Property(
     *                      property="message",
     *                      type="string",
     *                      example="Tag deleted successfully",
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response="400", description="User is unauthorized to delete this tag"),
     *      @OA\Response(response="401", ref="#/components/responses/Unauthenticated"),
     *      @OA\Response(response="404", ref="#/components/responses/NotFound"),
     * )
     */
    public function delete(Request $request, Tag $tag)
    {
        $user = $request->user();

        if ($tag->user_id !== $user->id) {
            return $this->sendError(__('Unauthorized'), [], 400);
        }

        $tag->delete();

        return $this->sendResponse(null, __('Tag deleted successfully'));
    }
}
