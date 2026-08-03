<?php

namespace App\Http\Requests;

class UpdateJobRequest extends StoreJobRequest
{
    public function rules(): array
    {
        return array_replace(parent::rules(), [
            'closing_date' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }
}
