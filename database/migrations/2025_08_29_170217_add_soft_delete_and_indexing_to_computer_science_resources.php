<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('computer_science_resources', function (Blueprint $table) {
            $table->softDeletes();
            $table->index('page_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('computer_science_resources', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropIndex(['page_url']);
        });
    }
};
