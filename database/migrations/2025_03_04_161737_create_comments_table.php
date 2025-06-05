<?php

use App\Models\Comment;
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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->morphs("commentable");
            $table->foreignIdFor(Comment::class, "root_comment_id")->nullable();
            $table->foreignIdFor(Comment::class, "parent_comment_id")->nullable();
            $table->foreignIdFor(User::class);

            $table->text("content");

            $table->smallInteger("depth")->default(1)->index();
            // Only root comment uses this
            $table->unsignedInteger("children_count")->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
