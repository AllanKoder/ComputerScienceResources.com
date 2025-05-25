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

            $table->decimal('community_rating')->storedAs("
                CASE WHEN review_count = 0 THEN 0 ELSE community / review_count END
            ")->index();

            $table->decimal('teaching_clarity_rating')->storedAs("
                CASE WHEN review_count = 0 THEN 0 ELSE teaching_clarity / review_count END
            ")->index();

            $table->decimal('engagement_rating')->storedAs("
                CASE WHEN review_count = 0 THEN 0 ELSE engagement / review_count END
            ")->index();

            $table->decimal('practicality_rating')->storedAs("
                CASE WHEN review_count = 0 THEN 0 ELSE practicality / review_count END
            ")->index();

            $table->decimal('user_friendliness_rating')->storedAs("
                CASE WHEN review_count = 0 THEN 0 ELSE user_friendliness / review_count END
            ")->index();

            $table->decimal('updates_rating')->storedAs("
                CASE WHEN review_count = 0 THEN 0 ELSE updates / review_count END
            ")->index();

            $table->decimal('overall_rating')
                ->storedAs('(community_rating +
                    teaching_clarity_rating +
                    engagement_rating +
                    practicality_rating +
                    user_friendliness_rating +
                    updates_rating)/6')
                ->index();

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
