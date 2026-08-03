<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The complete testimonials migration was already run immediately before
        // this duplicate legacy migration. Keep this migration as a safe no-op so
        // applying pending SmartWorker migrations never alters existing records.
    }

    public function down(): void
    {
        // This migration owns no schema, so it must not remove the table created
        // by the earlier testimonials migration.
    }
};
