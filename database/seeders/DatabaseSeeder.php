<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\BlogPost;
use App\Models\Testimonial;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin user ───────────────────────────────────
        $admin = User::create([
            'name'     => 'Admin',
            'email'    => 'admin@smartworker.com',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);
        UserProfile::create(['user_id' => $admin->id]);

        // ── Demo employer ─────────────────────────────────
        $employer = User::create([
            'name'     => 'Demo Employer',
            'email'    => 'employer@smartworker.com',
            'password' => Hash::make('password'),
            'role'     => 'employer',
        ]);
        UserProfile::create(['user_id' => $employer->id]);

        // ── Demo seeker ───────────────────────────────────
        $candidate = User::create([
            'name'     => 'Demo Candidate',
            'email'    => 'candidate@smartworker.com',
            'password' => Hash::make('password'),
            'role'     => 'candidate',
        ]);
        UserProfile::create(['user_id' => $candidate->id]);

        // ── Categories ────────────────────────────────────
        $categories = [
            ['name' => 'Finance',              'icon' => 'ti-money'],
            ['name' => 'IT & Engineering',     'icon' => 'ti-desktop'],
            ['name' => 'Education/Training',   'icon' => 'ti-book'],
            ['name' => 'Art/Design',           'icon' => 'ti-brush'],
            ['name' => 'Sales/Marketing',      'icon' => 'ti-world'],
            ['name' => 'Healthcare',           'icon' => 'ti-heart'],
            ['name' => 'Science',              'icon' => 'ti-filter'],
            ['name' => 'Food Services',        'icon' => 'ti-cup'],
            ['name' => 'Construction',         'icon' => 'ti-home'],
            ['name' => 'Logistics',            'icon' => 'ti-truck'],
        ];

        foreach ($categories as $cat) {
            Category::create([
                'name'      => $cat['name'],
                'slug'      => \Illuminate\Support\Str::slug($cat['name']),
                'icon'      => $cat['icon'],
                'is_active' => true,
            ]);
        }

        BlogPost::updateOrCreate(
            ['slug' => 'how-to-write-a-strong-job-application'],
            [
                'title' => 'How to Write a Strong Job Application',
                'excerpt' => 'Practical steps for tailoring your application and making a clear first impression.',
                'body' => 'Start by reading the job description carefully and identifying the skills the employer values most. Use your cover letter to connect those requirements to specific results from your experience. Keep your application focused, accurate, and easy to scan.',
                'author_name' => 'SmartWorker Team',
                'is_published' => true,
                'published_at' => now(),
            ],
        );

        Testimonial::updateOrCreate(
            ['name' => 'Demo Candidate'],
            [
                'user_id' => $candidate->id,
                'headline' => 'Candidate',
                'content' => 'SmartWorker makes it simple to keep my profile, resume, saved jobs, and applications in one place.',
                'is_active' => true,
            ],
        );
    }
}
