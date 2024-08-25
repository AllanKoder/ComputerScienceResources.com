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
        $table->unsignedBigInteger('user_id')->nullable(false)->index();
        $table->unsignedBigInteger('commentable_id'); // Polymorphic relation
        $table->string('commentable_type'); // Polymorphic relation
        $table->index(['commentable_id', 'commentable_type']);
        
        $table->timestamps();
        $table->foreign('user_id')->references('id')->on('users');
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