<?php

namespace App\Services;

use App\DTO\TaskDTO;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskService
{
    /**
     * Get a list of tasks based on filters and pagination.
     *
     * @param Request $request
     * @return array
     */
    public function getItems(Request $request): array
    {
        $query = Task::when($request->filterSearch, function ($query) use ($request) {
            $query->where('name', 'like', "%" . $request->filterSearch . "%")
                ->orWhere('description', 'like', "%" . $request->filterSearch . "%");
        })
            ->when(isset($request->sortBy) && isset($request->sortDesc), function ($query) use ($request) {
                $query->orderBy($request->sortBy, $request->sortDesc ? 'desc' : 'asc');
            })
            ->when(!isset($request->sortBy) || !isset($request->sortDesc), function ($query) {
                $query->orderBy('name', 'asc');
            });

        $count = $query->count();

        $data = $query->when($request->page && $request->itemsPerPage, function ($query) use ($request) {
            $query->skip(($request->page - 1) * $request->itemsPerPage)
                ->take($request->itemsPerPage);
        })
            ->get();

        return [
            'data'  => $data,
            'count' => $count,
        ];
    }

    /**
     * Create a new task.
     *
     * @param TaskDTO $data
     * @return Task
     */
    public function create(TaskDTO $data): Task
    {
        return Task::create($data->toArray());
    }

    /**
     * Find a task by ID.
     *
     * @param int $id
     * @return Task|null
     */
    public function find(int $id): ?Task
    {
        return Task::find($id);
    }

    /**
     * Update an existing task.
     *
     * @param Task $task
     * @param TaskDTO $data
     * @return Task
     */
    public function update(Task $task, TaskDTO $data): Task
    {
        $task->update($data->toArray());
        return $task;
    }

    /**
     * Delete a task.
     *
     * @param Task $task
     * @return void
     */
    public function delete(Task $task): void
    {
        $task->delete();
    }
}
