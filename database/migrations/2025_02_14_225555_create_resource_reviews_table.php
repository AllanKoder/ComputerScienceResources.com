<?php

use App\Models\ComputerScienceResource;
use App\Models\User;
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
        Schema::create('resource_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->index();
            $table->foreignIdFor(ComputerScienceResource::class)->index();

            $table->unique([
                'user_id',
                'computer_science_resource_id',
            ]);

            // Text
            $table->string('title');
            $table->longText('description');

            // Review score
            $table->smallInteger('community');
            $table->smallInteger('teaching_clarity');
            $table->smallInteger('engagement');
            $table->smallInteger('practicality');
            $table->smallInteger('user_friendliness');
            $table->smallInteger('updates');

            // Pros and Cons
            $table->json('pros')->nullable();
            $table->json('cons')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_reviews');
    }
};
