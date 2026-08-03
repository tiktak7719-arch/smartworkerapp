<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Convert the original enum to a portable string column before updating
     * persisted values. This works for both existing MySQL installations and
     * fresh SQLite development databases.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('role', 30)->default('candidate')->change();
        });

        DB::table('users')->where('role', 'seeker')->update(['role' => 'candidate']);
    }

    public function down(): void
    {
        DB::table('users')->where('role', 'candidate')->update(['role' => 'seeker']);

        Schema::table('users', function (Blueprint $table): void {
            $table->enum('role', ['admin', 'employer', 'seeker'])->default('seeker')->change();
        });
    }
};
