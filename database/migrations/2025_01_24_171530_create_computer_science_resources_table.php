<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('computer_science_resources')) {
            Schema::create('computer_science_resources', function (Blueprint $table) {
                $table->id();

                // User who posted
                $table->foreignIdFor(User::class);

                $table->string('name')->fulltext();
                $table->text('description')->fulltext();
                $table->string('image_url');
                $table->string('page_url');

                $table->set('platforms', ['book', 'podcast', 'youtube_channel', 'blog', 'website', 'organization', 'bootcamp', 'newsletter', 'workshop', 'course', 'forum', 'mobile_app', 'desktop_app', 'magazine'])
                    ->index();
                $table->enum('difficulty', ['beginner', 'industry_simple', 'industry_standard', 'industry_professional', 'academic'])
                    ->index();
                $table->enum('pricing', ['free', 'premium', 'paid', 'freemium'])
                    ->index();

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('computer_science_resources');
    }
};
