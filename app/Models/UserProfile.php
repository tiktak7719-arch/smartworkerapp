<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        // candidate
        'resume_path', 'resume_original_name', 'skills',
        'experience_years', 'education', 'linkedin', 'github', 'portfolio',
        // employer
        'company_name', 'company_website', 'company_logo',
        'industry', 'company_size', 'company_description',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getSkillsArrayAttribute(): array
    {
        return $this->skills
            ? array_map('trim', explode(',', $this->skills))
            : [];
    }
}
