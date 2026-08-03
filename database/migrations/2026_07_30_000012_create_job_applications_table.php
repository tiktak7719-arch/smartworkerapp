<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_listing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();           // seeker
            $table->string('cover_letter')->nullable();
            $table->string('cv_path')->nullable();                                    // uploaded CV file path
            $table->string('cv_original_name')->nullable();
            $table->enum('status', ['pending', 'reviewing', 'shortlisted', 'rejected', 'hired'])
                  ->default('pending');
            $table->text('employer_notes')->nullable();
            $table->timestamps();

            $table->unique(['job_listing_id', 'user_id']);                            // one application per job
            $table->index(['user_id', 'status']);
            $table->index(['job_listing_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
