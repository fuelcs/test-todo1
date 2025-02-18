<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'            => 'sometimes|required|string|max:255',
            'description'     => 'nullable|string',
            'completion_date' => 'sometimes|required|date',
            'completed'       => 'sometimes|boolean',
        ];
    }
}
