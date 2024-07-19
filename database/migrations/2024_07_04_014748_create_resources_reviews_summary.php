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
            $table->unsignedBigInteger('resource_id')->nullable(false);
            $table->integer('community_size_total')->default(1);
            $table->integer('teaching_explanation_clarity_total')->default(1);
            $table->integer('technical_depth_total')->default(1);
            $table->integer('practicality_to_industry_total')->default(1);
            $table->integer('user_friendliness_total')->default(1);
            $table->integer('updates_and_maintenance_total')->default(1);
            $table->integer('total_reviews')->default(1);
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
