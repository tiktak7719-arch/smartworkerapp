<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();           // employer
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('company');
            $table->string('company_logo')->nullable();
            $table->string('website')->nullable();
            $table->string('tagline')->nullable();
            $table->string('location')->nullable();
            $table->enum('job_type', ['full-time', 'part-time', 'remote', 'freelance', 'internship'])->default('full-time');
            $table->string('salary_min')->nullable();
            $table->string('salary_max')->nullable();
            $table->string('salary_display')->nullable();                             // e.g. "$80k – $100k"
            $table->longText('description');
            $table->text('requirements')->nullable();
            $table->text('benefits')->nullable();
            $table->string('tags')->nullable();
            $table->string('application_email')->nullable();
            $table->string('application_url')->nullable();
            $table->date('closing_date')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('views')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'is_featured', 'created_at']);
            $table->index('category_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_listings');
    }
};
