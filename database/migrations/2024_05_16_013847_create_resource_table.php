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
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->string('title')->fulltext();
            $table->text('description')->fulltext();
            $table->string('image_url')->default('');
            $table->json('formats')->index();
            $table->json('features')->nullable(); 
            $table->json('limitations')->nullable(); 
            $table->string('resource_url');
            $table->string('pricing')->index();
            $table->json('topics')->nullable(); 
            $table->string('difficulty')->index(); 

            // resource review summaries
            $table->integer('community_size_total')->default(0)->index();
            $table->integer('teaching_explanation_clarity_total')->default(0)->index();
            $table->integer('technical_depth_total')->default(0)->index();
            $table->integer('practicality_to_industry_total')->default(0)->index();
            $table->integer('user_friendliness_total')->default(0)->index();
            $table->integer('updates_and_maintenance_total')->default(0)->index();
            $table->integer('total_reviews')->default(0); 

            $table->unsignedBigInteger('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users');
        
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
        Schema::dropIfExists('resources');
    }
};

