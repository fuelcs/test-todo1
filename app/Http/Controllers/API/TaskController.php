<?php

namespace App\Http\Controllers\API;

use App\DTO\TaskDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\FilterTaskRequest;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\Task\TaskResource;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @OA\Tag(
 *     name="Tasks",
 *     description="API Endpoints of Tasks"
 * )
 */
class TaskController extends Controller
{
    protected TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Get a list of tasks with filters and pagination.
     *
     * @OA\Get(
     *     path="/api/tasks",
     *     summary="Get list of tasks",
     *     tags={"Tasks"},
     *     @OA\Parameter(
     *         name="filterSearch",
     *         in="query",
     *         description="Search term for filtering tasks by name or description",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sortBy",
     *         in="query",
     *         description="Field to sort by (name, description, completion_date, completed)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"name", "description", "completion_date", "completed"})
     *     ),
     *     @OA\Parameter(
     *         name="sortDesc",
     *         in="query",
     *         description="Sort direction: true for descending, false for ascending",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="itemsPerPage",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Task")),
     *             @OA\Property(property="count", type="integer")
     *         )
     *     )
     * )
     *
     * @param FilterTaskRequest $request
     * @return JsonResponse
     */
    public function index(FilterTaskRequest $request): JsonResponse
    {
        $items = $this->taskService->getItems($request);
        return response()->json([
            'data'  => TaskResource::collection($items['data']),
            'count' => $items['count'],
        ], 200);
    }

    /**
     * Create a new task.
     *
     * @OA\Post(
     *     path="/api/tasks",
     *     summary="Create a new task",
     *     tags={"Tasks"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/TaskDTO")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Task created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Task")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     *
     * @param StoreTaskRequest $request
     * @return TaskResource
     */
    public function store(StoreTaskRequest $request): TaskResource
    {
        $dto = new TaskDTO($request->validated());
        $task = $this->taskService->create($dto);
        return new TaskResource($task);
    }

    /**
     * Get a specific task by its ID.
     *
     * @OA\Get(
     *     path="/api/tasks/{id}",
     *     summary="Get task information",
     *     tags={"Tasks"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the task to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Task")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Task not found"
     *     )
     * )
     *
     * @param int $id
     * @return TaskResource|JsonResponse
     */
    public function show(int $id): TaskResource|JsonResponse
    {
        $task = $this->taskService->find($id);
        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }
        return new TaskResource($task);
    }

    /**
     * Update an existing task.
     *
     * @OA\Put(
     *     path="/api/tasks/{id}",
     *     summary="Update an existing task",
     *     tags={"Tasks"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the task to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/TaskDTO")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Task")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Task not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     *
     * @param UpdateTaskRequest $request
     * @param int $id
     * @return TaskResource|JsonResponse
     */
    public function update(UpdateTaskRequest $request, int $id): TaskResource|JsonResponse
    {
        $task = $this->taskService->find($id);
        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }
        $dto = new TaskDTO($request->validated());
        $task = $this->taskService->update($task, $dto);
        return new TaskResource($task);
    }

    /**
     * Delete a task.
     *
     * @OA\Delete(
     *     path="/api/tasks/{id}",
     *     summary="Delete a task",
     *     tags={"Tasks"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the task to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Task not found"
     *     )
     * )
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $task = $this->taskService->find($id);
        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }
        $this->taskService->delete($task);
        return response()->json(['message' => 'Task deleted']);
    }
}
