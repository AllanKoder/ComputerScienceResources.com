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
        Schema::create('resource_edits', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            
            // The resource we are editting
            $table->foreignIdFor(ComputerScienceResource::class)->constrained()->cascadeOnDelete();
            // The user who created the edit
            $table->foreignIdFor(User::class);

            // Reasoning behind the edit
            $table->string('edit_title');
            $table->text('edit_description');

            // Copied Schema of Computer Science Resource
            $table->string('name')->fulltext();
            $table->text('description')->fulltext();
            $table->string('image_url');
            
            // TODO: Have a url for each platform the resource is on.
            $table->string('page_url');

            $table->set('platforms', ['book', 'podcast', 'youtube_channel', 'blog', 'website', 'organization', 'bootcamp', 'newsletter', 'workshop', 'course', 'forum', 'mobile_app', 'desktop_app', 'magazine'])
                ->index();
            $table->enum('difficulty', ['beginner', 'industry_simple', 'industry_standard', 'industry_professional', 'academic'])
                ->index();
            $table->enum('pricing', ['free', 'premium', 'paid', 'freemium'])
                ->index();


            // Handle Tags:
            // 'topic_tags', 'programming_language_tags', 'general_tags'
            $table->json('topic_tags');
            $table->json('programming_language_tags');
            $table->json('general_tags');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_edits');
    }
};
