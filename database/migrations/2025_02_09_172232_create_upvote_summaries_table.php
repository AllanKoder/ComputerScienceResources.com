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
            $table->bigInteger('upvotes')->default(0);
            $table->bigInteger('downvotes')->default(0);

            $table->integer('score')->storedAs('upvotes - downvotes');
            $table->integer('total_votes')->storedAs('upvotes + downvotes');
            $table->integer('controversy')->storedAs('(upvotes + downvotes) - ABS(upvotes - downvotes)');

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
