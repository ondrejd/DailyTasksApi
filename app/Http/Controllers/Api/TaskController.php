<?php

namespace App\Http\Controllers\Api;

use App\Enums\TaskStatusEnum;
use App\Models\Tag;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * @OA\Tag(
 *     name="Tasks",
 *     description="Tasks API",
 * )
 */
class TaskController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/task",
     *     summary="List tasks",
     *     description="Listing of all tasks of the current user.",
     *     tags={"Tasks"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response="200",
     *          description="Listed tasks",
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
     *                      @OA\Items(ref="#/components/schemas/Task")
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
            $request->user()->tasks->map(fn (Task $task) => [
                'id' => $task->id,
                'name' => $task->name,
                'targeted_at' => $task->targeted_at,
                'fulfilled_at' => $task->fulfilled_at,
                'status' => $task->status,
                'tags' => $task->tags->map(fn (Tag $tag) => [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'color' => $tag->color,
                ])->toArray(),
            ])->toArray()
        );
    }

    /**
     * @OA\Post(
     *     path="/api/task",
     *     summary="Create new task",
     *     description="Create new task for the current user.",
     *      tags={"Tasks"},
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          request="Task",
     *          description="Task object that needs to be added to the store",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      type="string",
     *                      property="name",
     *                      description="Name of the new task",
     *                      example="New task",
     *                  ),
     *                  @OA\Property(
     *                      type="string",
     *                      format="date-time",
     *                      property="targeted_at",
     *                      description="Date-time on which is task targeted.",
     *                      example="2026-01-02 13:45:56",
     *                  ),
     *                  @OA\Property(
     *                      type="string",
     *                      format="date-time",
     *                      property="fulfilled_at",
     *                      description="Date-time on which was task fulfilled.",
     *                      example="2026-01-02 13:46:00",
     *                  ),
     *                  @OA\Property(
     *                      type="string",
     *                      property="status",
     *                      description="Status of the task (default value `ongoing`).",
     *                      example="ongoing",
     *                      ref="#/components/schemas/TaskStatusEnum",
     *                  ),
     *                  required={"name", "targeted_at"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Task created successfully",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="success",
     *                      type="bool",
     *                      example=true,
     *                  ),
     *                  @OA\Property(property="data", ref="#/components/schemas/Task"),
     *                  @OA\Property(
     *                      property="message",
     *                      type="string",
     *                      example="Task created successfully",
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response="401", ref="#/components/responses/Unauthenticated"),
     *      @OA\Response(response="422", ref="#/components/responses/ValidationError"),
     * )
     * 
     * @todo Allow to add tags...
     */
    public function create(Request $request)
    {
        // Todo make normal request class
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string'],
            'targeted_at' => ['required', 'date'],
            'fulfilled_at' => 'date',
            'status' => [Rule::enum(TaskStatusEnum::class)],
        ]);

        $task = new Task($validator->validated());
        $request->user()->tasks->save($task);

        // TODO Make normal response class with OpenApi annotations...
        return $this->sendResponse(
            [
                'id' => $task->id,
                'name' => $task->name,
                'targeted_at' => $task->targeted_at,
                'fulfilled_at' => $task->fulfilled_at,
                'status' => $task->status,
                'tags' => $task->tags->map(fn (Tag $tag) => [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'color' => $tag->color,
                ])->toArray(),
            ],
            __('Task created succesfully')
        );
    }

    /**
     * Display the specified resource.
     */
    public function detail(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Task $task)
    {
        //
    }
}
