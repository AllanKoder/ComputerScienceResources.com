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
    Schema::create('comments', function (Blueprint $table) {
    $table->id();
    $table->text('comment_text');
    $table->text('comment_title')->nullable();
    $table->unsignedBigInteger('user_id');
    $table->unsignedBigInteger('parent_id')->nullable();
    $table->unsignedBigInteger('commentable_id')->nullable(); // Polymorphic relation
    $table->string('commentable_type')->nullable(); // Polymorphic relation
    $table->timestamps();

    $table->foreign('user_id')->references('id')->on('users');
    $table->foreign('parent_id')->references('id')->on('comments');
    // No need to add foreign keys for polymorphic relations
    });
}

    /**
    * Reverse the migrations.
    *
    * @return void
    */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
};