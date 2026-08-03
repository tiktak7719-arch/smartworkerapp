<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class JobListing extends Model
{
    protected $fillable = [
        'user_id', 'category_id', 'title', 'slug', 'company',
        'company_logo', 'website', 'tagline', 'location', 'latitude', 'longitude', 'job_type',
        'salary_min', 'salary_max', 'salary_display', 'description',
        'requirements', 'benefits', 'tags', 'application_email',
        'application_url', 'closing_date', 'is_featured', 'is_active', 'views',
    ];

    protected function casts(): array
    {
        return [
            'closing_date' => 'date',
            'is_featured'  => 'boolean',
            'is_active'    => 'boolean',
            'latitude'     => 'float',
            'longitude'    => 'float',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (self $job) {
            if (empty($job->slug)) {
                $base = Str::slug($job->title . '-' . $job->company);
                $slug = $base;
                $i    = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $i++;
                }
                $job->slug = $slug;
            }
        });
    }

    /* ── Relationships ──────────────────────────────────── */
    public function employer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public function savedByUsers(): HasMany
    {
        return $this->hasMany(SavedJob::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    /* ── Scopes ─────────────────────────────────────────── */
    public function scopeActive($q)   { return $q->where('is_active', true); }
    public function scopeFeatured($q) { return $q->where('is_featured', true); }

    public function scopeFilter($query, array $filters): void
    {
        $query->when($filters['search'] ?? null, fn ($q, $s) =>
            $q->where(fn ($q) =>
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('company', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%")
            )
        );
        $query->when($filters['location'] ?? null, fn ($q, $l) =>
            $q->where('location', 'like', "%{$l}%")
        );
        $query->when($filters['category'] ?? null, fn ($q, $c) =>
            $q->whereHas('category', fn ($q) => $q->where('slug', $c))
        );
        $query->when($filters['job_type'] ?? null, fn ($q, $t) =>
            $q->where('job_type', $t)
        );
        $query->when($filters['min_salary'] ?? null, fn ($q, $salary) =>
            $q->where(fn ($q) => $q->whereNull('salary_max')->orWhere('salary_max', '>=', $salary))
        );
        $query->when($filters['max_salary'] ?? null, fn ($q, $salary) =>
            $q->where(fn ($q) => $q->whereNull('salary_min')->orWhere('salary_min', '<=', $salary))
        );
    }

    /* ── SEO route key ──────────────────────────────────── */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /* ── Helpers ────────────────────────────────────────── */
    public function getTagsArrayAttribute(): array
    {
        return $this->tags
            ? array_map('trim', explode(',', $this->tags))
            : [];
    }

    public function isClosingSoon(): bool
    {
        return $this->closing_date && $this->closing_date->diffInDays(now()) <= 7;
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }
}
