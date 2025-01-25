<?php

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
        Schema::create('computer_science_resources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('image_url');
            $table->string('page_url');
            $table->date('resource_created_on');

            $table->set('resource_type', ['book', 'podcast', 'youtube channel', 'blog', 'website', 'organization', 'bootcamp', 'newsletter', 'workshop', 'course', 'forum', 'mobile app', 'desktop app', 'e-zine']);
            $table->enum('difficulty', ['beginner', 'industry_simple', 'industry_standard', 'industry_professional', 'academic']);
            $table->enum('pricing', ['free', 'premium', 'paid', 'freemium']);

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('computer_science_resources');
    }
};
