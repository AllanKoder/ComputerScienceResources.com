Schema::create('computer_science_resource', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description');
    $table->string('image_url');
    $table->string('page_url');
    $table->date('created_at');

    $table->set('resource_type', ['book', 'podcast', 'youtube channel', 'blog', 'website', 'organization', 'bootcamp', 'newsletter', 'workshop', 'course', 'forum', 'mobile app', 'desktop app', 'e-zine']);
    $table->enum(difficulty, ['beginner', 'industry_simple', 'industry_standard', 'industry_professional', 'academic']);
    $table->enum('pricing', ['free', 'premium', 'paid', 'freemium'])

    $table->timestamps();
    
});
