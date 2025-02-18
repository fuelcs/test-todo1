<?php

namespace App\Http\Resources\Task;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="TaskResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Sample Task"),
 *     @OA\Property(property="description", type="string", example="This is a sample task description."),
 *     @OA\Property(property="completion_date", type="string", format="date", example="2025-12-31"),
 *     @OA\Property(property="completed", type="boolean", example=false),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-02-18T12:34:56Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-02-18T12:34:56Z")
 * )
 */
class TaskResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'description'     => $this->description,
            'completion_date' => $this->completion_date,
            'completed'       => $this->completed,
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,
        ];
    }
}
