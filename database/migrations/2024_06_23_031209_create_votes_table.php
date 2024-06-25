<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->morphs('voteable'); // This will create voteable_id and voteable_type
            $table->tinyInteger('vote_type'); // 1 for upvote, -1 for downvote
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Add a composite unique index if you want to prevent duplicate votes for the same voteable by the same user
            $table->unique(['user_id', 'voteable_id', 'voteable_type'], 'user_voteable_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('votes');
    }
};
