<?php

use App\Models\ComputerScienceResource;
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
        Schema::create('resource_review_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ComputerScienceResource::class);

            $table->bigInteger('community')->default(0);
            $table->bigInteger('teaching_clarity')->default(0);
            $table->bigInteger('engagement')->default(0);
            $table->bigInteger('practicality')->default(0);
            $table->bigInteger('user_friendliness')->default(0);
            $table->bigInteger('updates')->default(0);

            $table->integer('review_count')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_review_summaries');
    }
};
