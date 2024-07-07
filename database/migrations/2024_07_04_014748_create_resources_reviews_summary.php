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
        Schema::create('resource_review_summaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('resource_id');
            $table->integer('community_size_total')->default(0);
            $table->integer('teaching_explanation_clarity_total')->default(0);
            $table->integer('technical_depth_total')->default(0);
            $table->integer('practicality_to_industry_total')->default(0);
            $table->integer('user_friendliness_total')->default(0);
            $table->integer('updates_and_maintenance_total')->default(0);
            $table->integer('total_reviews')->default(0);
            $table->timestamps();

            // foreign key constraint
            $table->foreign('resource_id')->references('id')->on('resources')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('resource_review_summaries');
    }
};
