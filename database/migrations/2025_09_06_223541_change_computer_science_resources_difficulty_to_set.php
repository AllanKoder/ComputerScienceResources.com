<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
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
        // Rollback: rename back and revert type
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
