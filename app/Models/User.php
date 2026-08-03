<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role',
        'avatar', 'phone', 'location', 'latitude', 'longitude', 'bio', 'is_active', 'is_verified', 'job_alerts_enabled',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
            'is_verified'       => 'boolean',
            'job_alerts_enabled' => 'boolean',
            'latitude'          => 'float',
            'longitude'         => 'float',
        ];
    }

    /* ── Role helpers ───────────────────────────────────── */
    public function isAdmin(): bool    { return $this->role === 'admin'; }
    public function isEmployer(): bool { return $this->role === 'employer'; }
    public function isCandidate(): bool { return $this->role === 'candidate'; }


    /* ── Relationships ──────────────────────────────────── */
    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function jobListings(): HasMany
    {
        return $this->hasMany(JobListing::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public function savedJobs(): HasMany
    {
        return $this->hasMany(SavedJob::class);
    }

    public function ratingsReceived(): HasMany
    {
        return $this->hasMany(Rating::class, 'rated_user_id');
    }

    public function ratingsGiven(): HasMany
    {
        return $this->hasMany(Rating::class, 'reviewer_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(SmartNotification::class);
    }

    public function candidateConversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'candidate_id');
    }

    public function employerConversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'employer_id');
    }

    /* ── Scopes ─────────────────────────────────────────── */
    public function scopeActive($query)  { return $query->where('is_active', true); }
    public function scopeAdmins($query)  { return $query->where('role', 'admin'); }
    public function scopeEmployers($q)   { return $q->where('role', 'employer'); }
    public function scopeCandidates($q)  { return $q->where('role', 'candidate'); }
}
