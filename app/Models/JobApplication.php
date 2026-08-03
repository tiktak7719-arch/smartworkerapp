<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplication extends Model
{
    protected $fillable = [
        'job_listing_id', 'user_id', 'cover_letter',
        'cv_path', 'cv_original_name', 'status', 'employer_notes',
    ];

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(JobListing::class, 'job_listing_id');
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending'     => '<span class="label label-warning">Pending</span>',
            'reviewing'   => '<span class="label label-info">Reviewing</span>',
            'shortlisted' => '<span class="label label-primary">Shortlisted</span>',
            'rejected'    => '<span class="label label-danger">Rejected</span>',
            'hired'       => '<span class="label label-success">Hired</span>',
            default       => '<span class="label label-default">Unknown</span>',
        };
    }

    public function getStatusMessageAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Your application has been sent to the employer.',
            'reviewing' => 'The employer is reviewing your application.',
            'shortlisted' => 'Good news - you have been shortlisted by the employer.',
            'rejected' => 'The employer has closed this application.',
            'hired' => 'Congratulations - the employer marked you as hired.',
            default => 'Your application is being processed.',
        };
    }
}
