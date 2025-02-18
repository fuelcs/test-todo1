<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Task",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/TaskDTO"),
 *         @OA\Schema(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="created_at", type="string", format="date-time", example="2025-02-18T12:34:56Z"),
 *             @OA\Property(property="updated_at", type="string", format="date-time", example="2025-02-18T12:34:56Z")
 *         )
 *     }
 * )
 */
class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'completion_date',
        'completed'
    ];
}
