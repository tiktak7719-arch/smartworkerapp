<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    /**
     * The database table used for job listings.
     *
     * Laravel reserves the `jobs` table for queued jobs, so listings live in
     * their own table.
     */
    protected $table = 'job_listings';

    /**
     * The attributes that may be mass assigned.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'company',
        'location',
        'salary',
        'description',
        'category',
        'tags',
        'application_url',
        'closing_date',
        'website',
        'tagline',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'closing_date' => 'date',
            'is_active'    => 'boolean',
        ];
    }
}
