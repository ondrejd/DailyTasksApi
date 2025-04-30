<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\TagCreateRequest;
use App\Http\Requests\TagDeleteRequest;
use App\Http\Requests\TagListRequest;
use App\Http\Requests\TagUpdateRequest;
use App\Http\Resources\TagResource;
use App\Http\Resources\TagResourceCollection;
use App\Models\Tag;

/**
 * @OA\Tag(
 *     name="Tags",
 *     description="Tags API",
 * )
 */
class TagController extends BaseController
{
    /**
     * @OA\Get(
     *      path="/api/tag",
     *      summary="List tags",
     *      description="Listing of all tags of the current user.",
     *      tags={"Tags"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response="200",
     *          description="Listed tags",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="data",
     *                      type="array",
     *                      @OA\Items(ref="#/components/schemas/Tag"),
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response="401", ref="#/components/responses/Unauthenticated"),
     * )
     */
    public function list(TagListRequest $request): TagResourceCollection
    {
        return new TagResourceCollection(request()->user()->tags);
    }

    /**
     * @OA\Post(
     *      path="/api/tag",
     *      summary="Create new tag",
     *      description="Create new tag for the current user.",
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
     *                      type="string",
     *                      property="name",
     *                      description="Name of the tag",
     *                      example="Personal",
     *                  ),
     *                  @OA\Property(
     *                      type="string",
     *                      property="color",
     *                      description="Color of the tag in hex format",
     *                      example="#0066aa",
     *                  ),
     *                  required={"name"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response="201",
     *          description="Tag created successfully",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="data", ref="#/components/schemas/Tag"),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response="401", ref="#/components/responses/Unauthenticated"),
     *      @OA\Response(response="422", ref="#/components/responses/ValidationError"),
     * )
     * 
     * @todo Move the description of request body to the TagCreateRequest class...
     */
    public function create(TagCreateRequest $request): TagResource
    {
        $tag = new Tag($request->validated());

        request()->user()->tags()->save($tag);

        return new TagResource($tag);
    }

    /**
     * @OA\Put(
     *      path="/api/tag/{id}",
     *      summary="Update selected tag",
     *      description="Update selected tag of the current user.",
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
     *                      type="string",
     *                      property="name",
     *                      description="Name of the tag",
     *                      example="Other",
     *                  ),
     *                  @OA\Property(
     *                      type="string",
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
     *                  @OA\Property(property="data", ref="#/components/schemas/Tag"),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response="401", ref="#/components/responses/Unauthenticated"),
     *      @OA\Response(response="403", ref="#/components/responses/Unauthorized"),
     *      @OA\Response(response="404", ref="#/components/responses/NotFound"),
     *      @OA\Response(response="422", ref="#/components/responses/ValidationError"),
     * )
     * 
     * @todo Move the description of request body to the TagUpdateRequest class...
     */
    public function update(TagUpdateRequest $request, Tag $tag): TagResource
    {
        $values = $request->validated();

        if ($values['name']) {
            $tag->name = $values['name'];
        }

        if ($values['color']) {
            $tag->color = $values['color'];
        }

        $tag->save();

        return new TagResource($tag);
    }

    /**
     * @OA\Delete(
     *      path="/api/tag/{id}",
     *      summary="Delete selected tag",
     *      description="Delete selected tag of the current user.",
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
     *      @OA\Response(response="401", ref="#/components/responses/Unauthenticated"),
     *      @OA\Response(response="403", ref="#/components/responses/Unauthorized"),
     *      @OA\Response(response="404", ref="#/components/responses/NotFound"),
     * )
     */
    public function delete(TagDeleteRequest $request, Tag $tag)
    {
        $tag->delete();

        return $this->sendResponse(null, __('Tag deleted successfully'));
    }
}
