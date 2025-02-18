<?php

namespace App\DTO;

/**
 * @OA\Schema(
 *     schema="TaskDTO",
 *     type="object",
 *     required={"name", "completion_date"},
 *     @OA\Property(property="name", type="string", example="Sample Task"),
 *     @OA\Property(property="description", type="string", example="This is a sample task description."),
 *     @OA\Property(property="completion_date", type="string", format="date", example="2025-12-31"),
 *     @OA\Property(property="completed", type="boolean", example=false)
 * )
 */
class TaskDTO
{
    public string $name;
    public ?string $description;
    public string $completion_date;
    public bool $completed;

    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->description = $data['description'] ?? null;
        $this->completion_date = $data['completion_date'];
        $this->completed = $data['completed'] ?? false;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'completion_date' => $this->completion_date,
            'completed' => $this->completed,
        ];
    }
}
