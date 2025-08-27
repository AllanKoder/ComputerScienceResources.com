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
        Schema::create('tag_frequencies', function (Blueprint $table) {
            $table->char('tag', 100);
            $table->char('type', 50);

            $table->primary(['tag', 'type']);
            $table->bigInteger('count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tag_frequencies');
    }
};
