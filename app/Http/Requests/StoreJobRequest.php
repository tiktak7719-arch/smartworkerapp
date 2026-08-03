<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isEmployer() || $this->user()?->isAdmin();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'company' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'location' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'job_type' => ['required', 'in:full-time,part-time,remote,freelance,internship'],
            'salary_min' => ['nullable', 'numeric', 'min:0', 'lte:salary_max'],
            'salary_max' => ['nullable', 'numeric', 'min:0', 'gte:salary_min'],
            'salary_display' => ['nullable', 'string', 'max:100'],
            'description' => ['required', 'string', 'min:20'],
            'requirements' => ['nullable', 'string'],
            'benefits' => ['nullable', 'string'],
            'tags' => ['nullable', 'string', 'max:255'],
            'application_email' => ['nullable', 'email', 'max:255'],
            'application_url' => ['nullable', 'url:http,https', 'max:255'],
            'closing_date' => ['nullable', 'date', 'after:today'],
            'website' => ['nullable', 'url:http,https', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'company_logo' => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Job title is required.',
            'company.required' => 'Company name is required.',
            'job_type.required' => 'Please select a job type.',
            'description.required' => 'Job description is required.',
            'description.min' => 'Description must be at least 20 characters.',
            'closing_date.after' => 'Closing date must be a future date.',
        ];
    }
}
