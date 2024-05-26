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
            $table->string('title');
            $table->text('description');
            $table->string('image_url')->default('');
            $table->json('features')->nullable(); // Changed to JSON to store an array of features
            $table->json('limitations')->nullable(); // Changed to JSON to store an array of limitations
            $table->string('resource_url');
            $table->string('pricing');
            $table->json('topics')->nullable(); // Assuming topics is an array of strings
            $table->string('difficulty'); // Changed to string to store the difficulty level
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

