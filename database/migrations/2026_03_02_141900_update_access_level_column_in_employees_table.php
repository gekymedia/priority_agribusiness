<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change access_level from ENUM to VARCHAR to support new roles
        DB::statement("ALTER TABLE employees MODIFY COLUMN access_level VARCHAR(50) NOT NULL DEFAULT 'admin'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original ENUM if needed
        DB::statement("ALTER TABLE employees MODIFY COLUMN access_level ENUM('viewer', 'caretaker', 'manager', 'admin') NOT NULL DEFAULT 'viewer'");
    }
};
