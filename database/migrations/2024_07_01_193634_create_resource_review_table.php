<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resource_reviews', function (Blueprint $table) {
            $table->id();
            $table->integer('community_size');
            $table->integer('teaching_explanation_clarity');
            $table->integer('practicality_to_industry');
            $table->integer('technical_depth');
            $table->integer('user_friendliness');
            $table->integer('updates_and_maintenance');
            $table->unsignedBigInteger('comment_id')->nullable();
            $table->unsignedBigInteger('resource_id');
            $table->unsignedBigInteger('user_id');

            $table->string('review_title');
            $table->text('review_description');

            $table->foreign('resource_id')->references('id')->on('resources');
            $table->foreign('user_id')->references('id')->on('users');

            $table->unique(['user_id', 'resource_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('resource_review');
    }
};
