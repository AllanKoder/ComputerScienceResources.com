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
            $table->foreignIdFor(ComputerScienceResource::class);
            // The user who created the edit
            $table->foreignIdFor(User::class);

            // Copied Schema of Computer Science Resource
            $table->string('new_name')->fulltext();
            $table->text('new_description')->fulltext();
            $table->string('new_image_url');
            
            // TODO: Have a url for each platform the resource is on.
            $table->string('new_page_url');

            $table->set('new_platforms', ['book', 'podcast', 'youtube_channel', 'blog', 'website', 'organization', 'bootcamp', 'newsletter', 'workshop', 'course', 'forum', 'mobile_app', 'desktop_app', 'magazine'])
                ->index();
            $table->enum('new_difficulty', ['beginner', 'industry_simple', 'industry_standard', 'industry_professional', 'academic'])
                ->index();
            $table->enum('new_pricing', ['free', 'premium', 'paid', 'freemium'])
                ->index();


            // Handle Tags:
            // 'topic_tags', 'programming_language_tags', 'general_tags'
            $table->array('new_topic_tags');
            $table->array('new_programming_language_tags');
            $table->array('new_general_tags');
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
