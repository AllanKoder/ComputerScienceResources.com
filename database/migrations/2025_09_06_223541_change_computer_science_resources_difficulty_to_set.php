<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Rename column and change type
        DB::statement("
            ALTER TABLE computer_science_resources
            CHANGE difficulty difficulties SET(
                'general',
                'introduction',
                'practical',
                'advanced',
                'academic'
            ) NOT NULL
        ");
    }

    public function down(): void
    {
        // Keep only the first value from the SET
        DB::statement("
            UPDATE computer_science_resources
            SET difficulties = SUBSTRING_INDEX(difficulties, ',', 1)
            WHERE difficulties IS NOT NULL AND difficulties != ''
        ");

        DB::statement("
            ALTER TABLE computer_science_resources
            CHANGE difficulties difficulty ENUM(
                'general',
                'introduction',
                'practical',
                'advanced',
                'academic'
            ) NOT NULL
        ");
    }
};
