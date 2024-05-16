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
        Schema::create('resource', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('field');
            $table->enum('resource_type', ['video', 'youtube', 'book', 'blog', 'interactive', 'podcast', 'physical', 'other']);
            $table->enum('pricing', ["free, subscription, one-time fee, freemium"]);
            $table->boolean('hands_on');
            $table->json('tags')->nullable();
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
        Schema::dropIfExists('resource');
    }
};
