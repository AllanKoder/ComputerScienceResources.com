<?php

use App\Models\Upvote;
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
        Schema::create('upvote_summaries', function (Blueprint $table) {
            $table->id();
            $table->morphs('upvotable');
            $table->unsignedBigInteger('upvotes')->default(0);
            $table->unsignedBigInteger('downvotes')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upvote_summaries');
    }
};
