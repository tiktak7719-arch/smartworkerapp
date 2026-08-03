<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DirectoryController;
use App\Http\Controllers\DocumentDownloadController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RatingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/language/{locale}', [LanguageController::class, 'set'])->name('language.set');

// Public pages and content directories.
Route::view('/about', 'info-page', [
    'pageTitle' => 'About SmartWorker',
    'heading' => 'Connecting Talent With Opportunity',
    'content' => 'SmartWorker is a job portal where candidates can discover opportunities and employers can find qualified people. Create the right account type to manage your profile, applications, jobs, and company information securely.',
])->name('about');
Route::view('/faq', 'info-page', [
    'pageTitle' => 'Frequently Asked Questions',
    'heading' => 'How Can We Help?',
    'content' => 'Candidates can register, complete a profile, upload a resume, save jobs, and apply. Employers can create a company profile, post jobs, and manage the applications received for their own listings.',
])->name('faq');
Route::view('/pricing', 'info-page', [
    'pageTitle' => 'Pricing',
    'heading' => 'Plans for Every Hiring Need',
    'content' => 'Contact the SmartWorker team to discuss the hiring plan that fits your organisation. Employers can start by creating a company profile and posting their first job.',
])->name('pricing');
Route::view('/privacy-policy', 'policy-page', [
    'pageTitle' => 'Privacy Policy',
    'heading' => 'How SmartWorker Handles Your Information',
    'lastUpdated' => 'August 2026',
    'sections' => [
        ['title' => 'Information we collect', 'body' => 'We store the account, profile, job, application, message, rating, and notification information needed to run SmartWorker. You choose whether to add optional profile details, a resume, and location coordinates.'],
        ['title' => 'How information is used', 'body' => 'Profile and job information is used to show jobs, help employers find candidates, deliver application updates, and provide skill and location-based recommendations.'],
        ['title' => 'Resume and document access', 'body' => 'Resumes are stored privately. A resume can be downloaded by its candidate, an authenticated employer permitted to browse resumes, or an administrator.'],
        ['title' => 'Your choices', 'body' => 'You can update your profile, control job-match alerts, and contact the SmartWorker team about your account information.'],
        ['title' => 'Security', 'body' => 'We use access controls, CSRF protection, password hashing, private document storage, validation, and security headers to help protect the platform. No online service can guarantee absolute security.'],
    ],
])->name('privacy-policy');
Route::view('/terms', 'policy-page', [
    'pageTitle' => 'Terms of Use',
    'heading' => 'Using SmartWorker Responsibly',
    'lastUpdated' => 'August 2026',
    'sections' => [
        ['title' => 'Account responsibility', 'body' => 'Keep your password private and provide accurate account and profile information. You are responsible for actions performed through your account.'],
        ['title' => 'Jobs and applications', 'body' => 'Employers are responsible for the accuracy of their job listings. Candidates are responsible for the information in their applications and documents.'],
        ['title' => 'Acceptable use', 'body' => 'Do not use SmartWorker to post unlawful, misleading, harmful, or infringing content, or to access another user’s information without permission.'],
        ['title' => 'Platform availability', 'body' => 'SmartWorker may update features or suspend accounts that violate these terms to help protect users and the platform.'],
        ['title' => 'Contact', 'body' => 'For questions about these terms or your account, please use the SmartWorker contact page.'],
    ],
])->name('terms');
Route::redirect('/job-alerts', '/notifications')->name('job-alerts');
Route::get('/candidates', [DirectoryController::class, 'candidates'])->name('candidates.index');
Route::get('/employers', [DirectoryController::class, 'employers'])->name('employers.index');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{post}', [BlogController::class, 'show'])->name('blog.show');
Route::redirect('/blog-full-width', '/blog', 301);
Route::redirect('/blog-left-sidebar', '/blog', 301);
Route::redirect('/single-post', '/blog', 301);

// Authentication.
Route::get('/my-account', [AuthController::class, 'showAccount'])->name('my-account');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// Contact messages are persisted through a Form Request.
Route::get('/contact', [ContactController::class, 'create'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Public job discovery.
Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
Route::redirect('/browse-jobs', '/jobs', 301);
Route::redirect('/jobs/job-details.html', '/jobs', 301);
Route::get('/jobs/{slug}', [JobController::class, 'show'])->name('jobs.show');
Route::get('/browse-categories', [JobController::class, 'categories'])->name('jobs.categories');

// Employer-only actions. Candidates cannot enter these routes.
Route::middleware(['auth', 'role:employer,admin'])->group(function (): void {
    Route::get('/employer/dashboard', [JobController::class, 'employerDashboard'])->name('employer.dashboard');
    Route::get('/post-job', [JobController::class, 'create'])->name('post-job');
    Route::post('/jobs', [JobController::class, 'store'])->name('jobs.store');
    Route::get('/manage-jobs', [JobController::class, 'manageIndex'])->name('manage-jobs');
    Route::get('/manage-job-page/{slug}', [JobController::class, 'edit'])->name('jobs.edit');
    Route::put('/jobs/{slug}', [JobController::class, 'update'])->name('jobs.update');
    Route::delete('/jobs/{slug}', [JobController::class, 'destroy'])->name('jobs.destroy');
    Route::get('/manage-applications', [JobController::class, 'applications'])->name('manage-applications');
    Route::patch('/applications/{id}/status', [JobController::class, 'updateApplicationStatus'])->name('applications.status');
    Route::get('/jobs/{slug}/matches', [JobController::class, 'matches'])->name('jobs.matches');
    Route::get('/browse-resumes', [ProfileController::class, 'browseResumes'])->name('browse-resumes');
    Route::get('/company-profile', [ProfileController::class, 'edit'])->name('company.profile');
});

// Candidate-only actions. Employers cannot enter these routes.
Route::middleware(['auth', 'role:candidate'])->group(function (): void {
    Route::get('/candidate/dashboard', [JobController::class, 'candidateDashboard'])->name('candidate.dashboard');
    Route::post('/jobs/{slug}/apply', [JobController::class, 'apply'])->name('jobs.apply');
    Route::post('/jobs/{slug}/save', [JobController::class, 'saveJob'])->name('jobs.save');
    Route::get('/saved-jobs', [JobController::class, 'savedJobs'])->name('saved-jobs');
    Route::redirect('/bookmarked', '/saved-jobs', 301)->name('bookmarked');
    Route::get('/my-applications', [JobController::class, 'myApplications'])->name('my-applications');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::get('/resume', fn () => redirect()->route('profile'))->name('resume');
});

// Shared account controls.
Route::middleware('auth')->group(function (): void {
    Route::get('/add-resume', [ProfileController::class, 'edit'])->name('add-resume');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/change-password', [ProfileController::class, 'showChangePassword'])->name('change-password');
    Route::post('/change-password', [ProfileController::class, 'changePassword'])->name('password.change');
    Route::get('/documents/profiles/{profile}/resume', [DocumentDownloadController::class, 'profileResume'])->name('profiles.resume.download');
    Route::get('/documents/applications/{application}/cv', [DocumentDownloadController::class, 'applicationCv'])->name('applications.cv.download');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/jobs/{job}/chat', [ChatController::class, 'start'])->name('chat.start');
    Route::get('/chat/{conversation}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{conversation}', [ChatController::class, 'store'])->name('chat.store');
    Route::post('/applications/{application}/rating', [RatingController::class, 'store'])->name('ratings.store');
});

// Administration remains separate from employer controls.
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/jobs', [AdminController::class, 'jobs'])->name('jobs');
    Route::patch('/jobs/{job}/toggle', [AdminController::class, 'toggleJob'])->name('jobs.toggle');
    Route::delete('/jobs/{job}', [AdminController::class, 'destroyJob'])->name('jobs.destroy');
    Route::get('/contacts', [AdminController::class, 'contacts'])->name('contacts');
    Route::get('/contacts/{contact}', [AdminController::class, 'showContact'])->name('contacts.show');
    Route::delete('/contacts/{contact}', [AdminController::class, 'destroyContact'])->name('contacts.destroy');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::patch('/users/{user}/toggle', [AdminController::class, 'toggleUser'])->name('users.toggle');
    Route::patch('/users/{user}/verify', [AdminController::class, 'toggleUserVerification'])->name('users.verify');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    Route::get('/applications', [AdminController::class, 'applications'])->name('applications');
});

// Compatibility for old template URLs and a malformed legacy link found in the original markup.
Route::redirect('/href=', '/post-job', 301);
Route::get('/{page}.html', function (string $page) {
    $map = [
        'index' => '/',
        'index-03' => '/',
        'about' => '/about',
        'add-resume' => '/add-resume',
        'blog' => '/blog',
        'blog-full-width' => '/blog',
        'blog-left-sidebar' => '/blog',
        'bookmarked' => '/saved-jobs',
        'browse-categories' => '/browse-categories',
        'browse-jobs' => '/jobs',
        'browse-resumes' => '/browse-resumes',
        'change-password' => '/change-password',
        'contact' => '/contact',
        'faq' => '/faq',
        'job-alerts' => '/job-alerts',
        'job-details' => '/jobs',
        'job-page' => '/jobs',
        'manage-applications' => '/manage-applications',
        'manage-job-page' => '/manage-jobs',
        'manage-jobs' => '/manage-jobs',
        'manage-resumes' => '/browse-resumes',
        'my-account' => '/my-account',
        'notifications' => '/notifications',
        'post-job' => '/post-job',
        'pricing' => '/pricing',
        'privacy-policy' => '/privacy-policy',
        'resume' => '/resume',
        'single-post' => '/blog',
    ];

    abort_unless(array_key_exists($page, $map), 404);

    return redirect($map[$page], 301);
});
