<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class FilterTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('sortDesc')) {
            $this->merge([
                'sortDesc' => filter_var($this->sortDesc, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'filterSearch'  => 'nullable|string',
            'sortBy'        => 'nullable|in:name,description,completion_date,completed',
            'sortDesc'      => 'nullable|boolean',
            'page'          => 'nullable|integer|min:1',
            'itemsPerPage'  => 'nullable|integer|min:1',
        ];
    }
}
